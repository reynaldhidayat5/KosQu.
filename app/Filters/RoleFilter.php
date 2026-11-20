<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Pemeriksa login & role. Pemakaian di Routes: ['filter' => 'role:customer,mitra']
 */
class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (! session()->get('isLoggedIn')) {
            // Activity Diagram Hal. 6: belum login -> arahkan ke Login / Register
            $next = '/' . ltrim($request->getUri()->getPath(), '/');
            if (strtolower($request->getMethod()) === 'get') {
                session()->set('redirect_after_login', $next);
            }
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        if ($arguments && ! in_array(session()->get('role'), $arguments, true)) {
            return redirect()->to('/')->with('error', 'Akses ditolak untuk role Anda.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
