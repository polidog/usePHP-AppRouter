<?php

declare(strict_types=1);

namespace Polidog\UsephpApprouter;

use Psr\Container\ContainerInterface;
use Polidog\UsePhp\Component\BaseComponent;
use Polidog\UsePhp\Component\ComponentInterface;
use Polidog\UsePhp\Runtime\Action;
use Polidog\UsePhp\Runtime\ComponentState;
use Polidog\UsephpApprouter\Component\ErrorPageComponent;
use Polidog\UsephpApprouter\Component\FunctionPage;
use Polidog\UsephpApprouter\Component\PageComponent;
use Polidog\UsephpApprouter\Document\DocumentInterface;
use Polidog\UsephpApprouter\Document\HtmlDocument;
use Polidog\UsephpApprouter\Layout\LayoutComponent;
use Polidog\UsephpApprouter\Layout\LayoutInterface;
use Polidog\UsephpApprouter\Layout\LayoutRenderer;
use Polidog\UsephpApprouter\Layout\LayoutStack;
use Polidog\UsephpApprouter\Router\RouteMatch;
use Polidog\UsephpApprouter\Router\Router;
use Polidog\UsephpApprouter\Router\RouterInterface;

class AppRouter
{
    private string $appDirectory;
    private ?ContainerInterface $container;
    private RouterInterface $router;
    private DocumentInterface $document;

    public function __construct(
        string $appDirectory,
        ?ContainerInterface $container = null,
    ) {
        $this->appDirectory = rtrim($appDirectory, '/');
        $this->container = $container;
        $this->router = Router::create($this->appDirectory);
        $this->document = new HtmlDocument();
    }

    public static function create(string $appDirectory): self
    {
        return new self($appDirectory);
    }

    public function setContainer(ContainerInterface $container): self
    {
        $this->container = $container;
        return $this;
    }

    public function setJsPath(string $path): self
    {
        if ($this->document instanceof HtmlDocument) {
            $this->document->setJsPath($path);
        }
        return $this;
    }

    public function addCssPath(string $path): self
    {
        if ($this->document instanceof HtmlDocument) {
            $this->document->addCssPath($path);
        }
        return $this;
    }

    public function setDocument(DocumentInterface $document): self
    {
        $this->document = $document;
        return $this;
    }

    public function run(): void
    {
        $path = $this->getRequestPath();

        $match = $this->router->match($path);

        if ($match === null) {
            $this->handleNotFound();
            return;
        }

        $this->handleMatch($match);
    }

    private function handleMatch(RouteMatch $match): void
    {
        $layoutStack = $this->loadLayouts($match->getLayoutPaths(), $match->getParams());

        $pageComponent = $this->loadPage($match->getPagePath(), $match->getParams());

        if ($pageComponent === null) {
            $this->handleNotFound();
            return;
        }

        $this->renderPage($pageComponent, $layoutStack, $match->getParams());
    }

    private function handleNotFound(): void
    {
        http_response_code(404);

        $errorPagePath = $this->router->getErrorPagePath();

        if ($errorPagePath !== null) {
            $errorComponent = $this->loadErrorPage($errorPagePath, 404, 'Page not found');

            if ($errorComponent !== null) {
                $rootLayoutPath = $this->appDirectory . '/layout.php';
                $layoutStack = new LayoutStack();

                if (file_exists($rootLayoutPath)) {
                    $layout = $this->loadLayoutFromFile($rootLayoutPath, []);
                    if ($layout !== null) {
                        $layoutStack->push($layout);
                    }
                }

                $this->renderPage($errorComponent, $layoutStack, []);
                return;
            }
        }

        echo $this->document->renderError(404, 'Page not found');
    }

    /**
     * @param array<string> $layoutPaths
     * @param array<string, string> $params
     */
    private function loadLayouts(array $layoutPaths, array $params): LayoutStack
    {
        $stack = new LayoutStack();

        foreach ($layoutPaths as $layoutPath) {
            $layout = $this->loadLayoutFromFile($layoutPath, $params);
            if ($layout !== null) {
                $stack->push($layout);
            }
        }

        return $stack;
    }

    /**
     * @param array<string, string> $params
     */
    private function loadLayoutFromFile(string $filePath, array $params): ?LayoutInterface
    {
        if (!file_exists($filePath)) {
            return null;
        }

        require_once $filePath;

        $className = $this->getClassFromFile($filePath);

        if ($className === null) {
            return null;
        }

        if (!class_exists($className)) {
            return null;
        }

        $instance = $this->resolveInstance($className);

        if (!$instance instanceof LayoutInterface) {
            return null;
        }

        if ($instance instanceof LayoutComponent) {
            $instance->setParams($params);
        }

        return $instance;
    }

    /**
     * @param array<string, string> $params
     */
    private function loadPage(string $pagePath, array $params): ComponentInterface|FunctionPage|null
    {
        if (!file_exists($pagePath)) {
            return null;
        }

        $result = require_once $pagePath;

        // Closure return: function-based page
        if ($result instanceof \Closure) {
            return $this->buildFunctionPage($result, $pagePath, $params);
        }

        // Class-based page (fallback)
        $className = $this->getClassFromFile($pagePath);

        if ($className !== null && class_exists($className)) {
            $instance = $this->resolveInstance($className);

            if ($instance instanceof ComponentInterface) {
                if ($instance instanceof PageComponent) {
                    $instance->setParams($params);
                }

                return $instance;
            }
        }

        return null;
    }

    /**
     * @param array<string, string> $params
     */
    private function buildFunctionPage(\Closure $factory, string $pagePath, array $params): FunctionPage
    {
        $context = new Component\PageContext($params);
        $renderFn = $factory($context);
        $pageId = $this->computePageId($pagePath);

        return new FunctionPage($renderFn, $context, $pageId);
    }

    private function computePageId(string $pagePath): string
    {
        $relative = str_replace($this->appDirectory, '', $pagePath);
        $relative = preg_replace('#/page\.php$#', '', $relative);

        if ($relative === '' || $relative === '/') {
            return '/';
        }

        return $relative;
    }

    private function loadErrorPage(string $errorPath, int $statusCode, string $message): ?ComponentInterface
    {
        if (!file_exists($errorPath)) {
            return null;
        }

        require_once $errorPath;

        $className = $this->getClassFromFile($errorPath);

        if ($className === null) {
            return null;
        }

        if (!class_exists($className)) {
            return null;
        }

        $instance = $this->resolveInstance($className);

        if (!$instance instanceof ComponentInterface) {
            return null;
        }

        if ($instance instanceof ErrorPageComponent) {
            $instance->setError($statusCode, $message);
        }

        return $instance;
    }

    /**
     * @param array<string, string> $params
     */
    private function renderPage(ComponentInterface|FunctionPage $page, LayoutStack $layouts, array $params): void
    {
        $componentId = $page instanceof FunctionPage
            ? $page->getComponentId()
            : 'page:' . $page::class;

        $state = ComponentState::getInstance($componentId);
        ComponentState::reset();

        // Handle useState action (onClick etc.) before rendering
        $this->dispatchStateAction($componentId, $state);

        if ($page instanceof BaseComponent) {
            $page->setComponentState($state);
        }

        if ($page instanceof PageComponent) {
            $page->dispatchActionFromRequest();
        }

        $pageElement = $page->render();

        if ($page instanceof FunctionPage && $this->document instanceof HtmlDocument) {
            $this->document->setMetadata($page->getMetadata());
        } elseif ($page instanceof PageComponent && $this->document instanceof HtmlDocument) {
            $this->document->setMetadata($page->getMetadata());
        }

        $renderer = new LayoutRenderer($componentId, $_SERVER['REQUEST_URI'] ?? '/');
        $html = $renderer->render($pageElement, $layouts);

        if (isset($_SERVER['HTTP_X_USEPHP_PARTIAL'])) {
            echo $html;
            return;
        }

        $wrappedHtml = sprintf(
            '<div data-usephp="%s">%s</div>',
            htmlspecialchars($componentId, ENT_QUOTES, 'UTF-8'),
            $html
        );

        $output = $this->document->render($wrappedHtml);

        echo $output;
    }

    /**
     * Handle useState setState actions from POST (onClick, onChange, etc.).
     */
    private function dispatchStateAction(string $componentId, ComponentState $state): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            return;
        }

        $actionJson = $_POST['_usephp_action'] ?? null;
        $postComponentId = $_POST['_usephp_component'] ?? null;

        if (!is_string($actionJson) || !is_string($postComponentId)) {
            return;
        }

        // Only handle JSON actions (not usephp-action: form tokens)
        if (str_starts_with($actionJson, 'usephp-action:')) {
            return;
        }

        if ($postComponentId !== $componentId) {
            return;
        }

        try {
            $actionData = json_decode($actionJson, true, 512, JSON_THROW_ON_ERROR);
            $action = Action::fromArray($actionData);

            if ($action->type === 'setState') {
                $index = $action->payload['index'] ?? 0;
                $value = $action->payload['value'] ?? null;
                $state->setState($index, $value);
            }
        } catch (\JsonException) {
            return;
        }

        // PRG pattern: redirect after state change (non-AJAX)
        if (!isset($_SERVER['HTTP_X_USEPHP_PARTIAL'])) {
            $redirectUrl = strtok($_SERVER['REQUEST_URI'] ?? '/', '?');
            header('Location: ' . $redirectUrl, true, 303);
            exit;
        }
    }

    /**
     * Resolve a class instance using the container or direct instantiation.
     */
    private function resolveInstance(string $className): object
    {
        if ($this->container !== null && $this->container->has($className)) {
            return $this->container->get($className);
        }

        return new $className();
    }

    private function getClassFromFile(string $filePath): ?string
    {
        $content = file_get_contents($filePath);

        if ($content === false) {
            return null;
        }

        $tokens = token_get_all($content);
        $tokenCount = count($tokens);
        $namespace = null;
        $className = null;

        for ($i = 0; $i < $tokenCount; $i++) {
            $token = $tokens[$i];

            if (!is_array($token)) {
                continue;
            }

            if ($token[0] === T_NAMESPACE) {
                $namespaceParts = [];
                $i++;

                while ($i < $tokenCount) {
                    $nextToken = $tokens[$i];

                    if ($nextToken === ';' || $nextToken === '{') {
                        break;
                    }

                    if (is_array($nextToken)) {
                        if ($nextToken[0] === T_NAME_QUALIFIED || $nextToken[0] === T_STRING) {
                            $namespaceParts[] = $nextToken[1];
                        }
                    }

                    $i++;
                }

                $namespace = implode('', $namespaceParts);
            }

            if ($token[0] === T_CLASS) {
                $i++;

                while ($i < $tokenCount) {
                    $nextToken = $tokens[$i];

                    if (is_array($nextToken) && $nextToken[0] === T_STRING) {
                        $className = $nextToken[1];
                        break;
                    }

                    if (is_array($nextToken) && $nextToken[0] === T_WHITESPACE) {
                        $i++;
                        continue;
                    }

                    break;
                }

                if ($className !== null) {
                    break;
                }
            }
        }

        if ($className === null) {
            return null;
        }

        if ($namespace !== null) {
            return $namespace . '\\' . $className;
        }

        return $className;
    }

    private function getRequestPath(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = parse_url($uri, PHP_URL_PATH);

        return $path ?? '/';
    }
}
