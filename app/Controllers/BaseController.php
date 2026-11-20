<?php

namespace App\Controllers;

use App\Models\MitraModel;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

abstract class BaseController extends Controller
{
    /** @var CLIRequest|IncomingRequest */
    protected $request;

    protected $helpers = ['url', 'form'];

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
    }

    /** Data mitra untuk user yang sedang login (selalu dibaca ulang dari DB agar status KYC & saldo akurat). */
    protected function mitraSaatIni(): ?array
    {
        $mitra = (new MitraModel())->where('id', session()->get('id'))->first();
        if ($mitra) {
            session()->set(['kd_mitra' => $mitra['kd_mitra'], 'status_kyc' => $mitra['status_kyc']]);
        }
        return $mitra;
    }

    /** Simpan satu file gambar upload ke public/uploads/<folder>; kembalikan nama file atau null. */
    protected function simpanGambar($file, string $folder): ?string
    {
        if (! $file || ! $file->isValid() || $file->hasMoved()) {
            return null;
        }
        $nama = $file->getRandomName();
        $file->move(FCPATH . 'uploads/' . $folder, $nama);
        return $nama;
    }
}
