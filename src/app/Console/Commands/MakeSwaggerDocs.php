<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class MakeSwaggerDocs extends Command
{
    protected $signature = 'make:swagger {controller : Nome do controller (ex: TicketController)}';
    protected $description = 'Gera arquivo de documentação Swagger para um controller';

    public function handle(): void
    {
        $controllerName = $this->argument('controller');
        $baseName       = Str::replaceLast('Controller', '', $controllerName);
        $tag            = $baseName;
        $namespace      = "App\\Http\\Swagger";
        $className      = "{$baseName}Docs";
        $directory      = app_path("Http/Swagger");
        $filePath       = "{$directory}/{$className}.php";

        if (file_exists($filePath)) {
            $this->error("Arquivo já existe: {$filePath}");
            return;
        }

        $routes = $this->resolveRoutes($controllerName);

        if (empty($routes)) {
            $this->error("Nenhuma rota encontrada para {$controllerName}");
            return;
        }

        $methods = $this->buildMethods($routes, $tag);
        $stub    = $this->buildStub($namespace, $className, $methods);

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        file_put_contents($filePath, $stub);

        $this->info("Arquivo gerado: {$filePath}");
    }

    private function resolveRoutes(string $controllerName): array
    {
        $routes = [];

        foreach (Route::getRoutes() as $route) {
            $action = $route->getActionName();

            if (!Str::contains($action, $controllerName)) {
                continue;
            }

            $method     = strtoupper($route->methods()[0]);
            $uri        = '/' . $route->uri();
            $actionName = Str::afterLast($action, '@');
            $protected  = in_array('auth:sanctum', $route->gatherMiddleware());

            $routes[] = compact('method', 'uri', 'actionName', 'protected');
        }

        return $routes;
    }

    private function buildMethods(array $routes, string $tag): string
    {
        $methods = '';

        foreach ($routes as $route) {
            $methods .= $this->buildMethod($route, $tag);
        }

        return $methods;
    }

    private function buildMethod(array $route, string $tag): string
    {
        $httpMethod  = Str::ucfirst(strtolower($route['method']));
        $attribute   = "OA\\{$httpMethod}";
        $path        = $route['uri'];
        $action      = $route['actionName'];
        $security    = $route['protected'] ? "\n        security: [['bearerAuth' => []]]," : '';
        $hasBody     = in_array($route['method'], ['POST', 'PUT', 'PATCH']);
        $requestBody = $hasBody ? $this->buildRequestBody() : '';
        $summary     = $this->resolveSummary($action, $tag);

        return <<<PHP

    #[{$attribute}(
        path: '{$path}',
        summary: '{$summary}',
        tags: ['{$tag}'],{$security}{$requestBody}
        responses: [
            new OA\Response(response: 200, description: 'Sucesso'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 422, description: 'Dados inválidos'),
        ]
    )]
    public function {$action}() {}

PHP;
    }

    private function buildRequestBody(): string
    {
        return "
        requestBody: new OA\\RequestBody(
            required: true,
            content: new OA\\JsonContent(
                properties: [
                    // TODO: adicionar propriedades
                ]
            )
        ),";
    }

    private function resolveSummary(string $action, string $tag): string
    {
        return match ($action) {
            'index'   => "Listar {$tag}s",
            'show'    => "Buscar {$tag} por ID",
            'store'   => "Criar {$tag}",
            'update'  => "Atualizar {$tag}",
            'destroy' => "Deletar {$tag}",
            default   => Str::headline($action),
        };
    }

    private function buildStub(string $namespace, string $className, string $methods): string
    {
        return <<<PHP
<?php

namespace {$namespace};

use OpenApi\Attributes as OA;

class {$className}
{{$methods}}
PHP;
    }
}
