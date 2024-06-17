<?php

namespace App\Controller\User;

use App\Controller\AbstractController;
use App\Library\Http\Response\FileDownloadResponse;
use App\Model\Entity\DownloadableFile;
use App\Model\Repository\DownloadableFileRepositoryInterface;
use App\Model\Service\DownloadableFile\DownloadableFileFilesystemInterface;
use App\Service\Menu\Registry\MenuTypeFactoryRegistryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\UuidV4;

class DownloadableFileController extends AbstractController
{
    private DownloadableFileRepositoryInterface $downloadableFileRepository;

    public function __construct(DownloadableFileRepositoryInterface $downloadableFileRepository)
    {
        $this->downloadableFileRepository = $downloadableFileRepository;
    }

    #[Route('/downloadable-files', name: 'user_downloadable_file_list')]
    public function list(MenuTypeFactoryRegistryInterface $menuFactory, Request $request): Response
    {
        $page = (int) $request->query->get('page', 1);
        $paginator = $this->downloadableFileRepository->getUserPaginator($page, 24);

        if ($paginator->isCurrentPageOutOfBounds())
        {
            throw $this->createNotFoundException();
        }

        $paginationMenu = $menuFactory->buildMenuType('pagination', ['paginator' => $paginator]);

        return $this->render('user/downloadable_file/list.html.twig', [
            'pagination_menu' => $paginationMenu,
            'paginator'       => $paginator,
            'breadcrumbs'     => $this->createBreadcrumbs(),
        ]);
    }

    #[Route('/downloadable-file/{id}/download', name: 'user_downloadable_file_download')]
    public function download(DownloadableFileFilesystemInterface $downloadableFileFilesystem,
                             UuidV4                              $id): FileDownloadResponse
    {
        $downloadableFile = $this->findDownloadableFileOrThrow404($id);
        $fileContents = $downloadableFileFilesystem->getFileContents($downloadableFile);

        if ($fileContents === null)
        {
            throw $this->createNotFoundException();
        }

        $fileName = $downloadableFile->getTitle();
        $fileExtension = $downloadableFile->getExtension();

        return new FileDownloadResponse($fileName, $fileExtension, $fileContents);
    }
    
    private function findDownloadableFileOrThrow404(UuidV4 $id): DownloadableFile
    {
        $downloadableFile = $this->downloadableFileRepository->findOneById($id);

        if ($downloadableFile === null)
        {
            throw $this->createNotFoundException();
        }

        return $downloadableFile;
    }
}