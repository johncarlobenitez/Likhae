<?php

namespace Tests\Feature;

use App\Models\LogisticsProvider;
use App\Models\Rider;
use App\Models\Seller;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class FullWorkspaceAuditTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_static_workspace_page_renders_for_its_role(): void
    {
        $this->withoutVite();
        $users = [];
        foreach (['admin', 'buyer', 'seller', 'logistics', 'rider'] as $role) {
            $users[$role] = User::factory()->create(['role' => $role, 'status' => 'active']);
        }
        Seller::create(['user_id' => $users['seller']->id, 'name' => 'Audit Shop', 'slug' => 'audit-shop', 'status' => 'approved']);
        $provider = LogisticsProvider::create(['user_id' => $users['logistics']->id, 'name' => 'Audit Fleet', 'slug' => 'audit-fleet', 'status' => 'approved']);
        Rider::create(['user_id' => $users['rider']->id, 'logistics_provider_id' => $provider->id, 'is_active' => true]);

        $failures = [];
        foreach (Route::getRoutes() as $route) {
            $role = explode('.', $route->getName() ?? '')[0];
            if (! isset($users[$role]) || ! in_array('GET', $route->methods(), true)
                || str_contains($route->uri(), '{') || str_ends_with($route->getName(), '.stream')) {
                continue;
            }
            $this->actingAs($users[$role]);
            $response = $this->get('/'.$route->uri());
            if (! in_array($response->getStatusCode(), [200, 302], true)) {
                $failures[] = $route->getName().': '.$response->getStatusCode();
            }
        }
        $this->assertSame([], $failures);
    }

    public function test_literal_view_references_match_file_case_for_linux_hosting(): void
    {
        $views = [];
        foreach (File::allFiles(resource_path('views')) as $file) {
            $name = str_replace(['/', '\\'], '.', substr($file->getRelativePathname(), 0, -10));
            $views[strtolower($name)] = $name;
        }
        $failures = [];
        foreach ([app_path(), base_path('routes'), resource_path('views')] as $root) {
            foreach (File::allFiles($root) as $file) {
                if ($file->getExtension() !== 'php') continue;
                preg_match_all('/(?:\bview|@extends|@include|@includeIf|@includeOnce)\(\s*[\'\"]([A-Za-z0-9_.-]+)[\'\"]/', $file->getContents(), $matches);
                foreach (array_unique($matches[1]) as $name) {
                    if (($views[strtolower($name)] ?? null) !== $name) {
                        $failures[] = $file->getRelativePathname().': '.$name.' => '.($views[strtolower($name)] ?? 'MISSING');
                    }
                }
            }
        }
        $this->assertSame([], $failures);
    }

    public function test_all_literal_template_routes_exist(): void
    {
        $failures = [];
        foreach (File::allFiles(resource_path('views')) as $file) {
            preg_match_all('/(?<!->)\broute\(\s*[\'\"]([A-Za-z0-9_.-]+)[\'\"]\s*[,)]/', $file->getContents(), $matches);
            foreach (array_unique($matches[1]) as $name) {
                if (! Route::has($name)) $failures[] = $file->getRelativePathname().': '.$name;
            }
        }
        $this->assertSame([], $failures);
    }
}
