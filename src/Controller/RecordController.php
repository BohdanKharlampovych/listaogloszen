<?php
/**
 * Record controller.
 */

namespace App\Controller;

use App\Entity\Record;
use App\Entity\Task;
use App\Form\Type\TaskType;
use App\Repository\TaskRepository;
use App\Form\Type\RecordType;
use App\Repository\RecordRepository;
use App\Service\RecordServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class RecordController.
 */
#[Route('/record')]
class RecordController extends AbstractController
{
    /**
     * Record service.
     */
    private RecordServiceInterface $recordService;

    /**
     * Translator.
     */
    private TranslatorInterface $translator;

    /**
     * Task service.
     */
    private TaskRepository $taskRepository;

    /**
     * Constructor.
     *
     * @param RecordServiceInterface $recordService  Record service
     * @param TranslatorInterface    $translator     Translator
     * @param TaskRepository         $taskRepository Task service
     */
    public function __construct(TaskRepository $taskRepository, RecordServiceInterface $recordService, TranslatorInterface $translator, Security $security)
    {
        $this->recordService = $recordService;
        $this->translator = $translator;
        $this->security = $security;
        $this->taskRepository = $taskRepository;
    }

    /**
     * Index action.
     *
     * @return Response HTTP response
     */
    #[Route(
        name: 'record_index',
        methods: 'GET'
    )]
    public function index(Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $pagination = $this->recordService->createPaginatedList($page);

        return $this->render(
            'record/index.html.twig',
            ['pagination' => $pagination]
        );
    }

    /**
     * Show action.
     *
     * @param RecordRepository $repository Record repository
     * @param int              $id         Record id
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{id}',
        name: 'record_show',
        // requirements: ['id' => '[1-9]\d*'],
        methods: 'GET'
    )]
    public function show(RecordRepository $repository, int $id, Record $record): Response
    {
        $record = $repository->find($id);

        return $this->render(
            'record/show.html.twig',
            ['record' => $record]
        );
    }

    #[Route('/create/record', name: 'record_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $record = new Record();
        $form = $this->createForm(
            RecordType::class,
            $record,
            [
                'method' => 'GET',
                'action' => $this->generateUrl('record_create'),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $record->setUser($this->getUser());
            $record->setVisibility(1);
            $this->recordService->save($record);

            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            return $this->redirectToRoute('record_index');
        }

        return $this->render('record/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/record/delete/{id}', name: 'record_delete', methods: ['GET', 'POST'])]
    public function delete(Request $request, Record $record): Response
    {
        $form = $this->createForm(
            FormType::class,
            $record,
            [
                'method' => 'DELETE',
                'action' => $this->generateUrl('record_delete', ['id' => $record->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->recordService->delete($record);

            $this->addFlash(
                'success',
                $this->translator->trans('message.deleted_successfully')
            );

            return $this->redirectToRoute('record_index');
        }

        return $this->render(
            'record/delete.html.twig',
            [
                'form' => $form->createView(),
                'record' => $record,
            ]
        );
    }

    #[Route('/{id}/edit', name: 'record_edit', requirements: ['id' => '[1-9]\d*'], methods: 'GET|PUT')]
    public function edit(Request $request, Record $record): Response
    {
        $form = $this->createForm(
            RecordType::class,
            $record,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('record_edit', ['id' => $record->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->recordService->save($record);

            $this->addFlash(
                'success',
                $this->translator->trans('message.edited_successfully')
            );

            return $this->redirectToRoute('record_index');
        }

        return $this->render(
            'record/edit.html.twig',
            [
                'form' => $form->createView(),
                'record' => $record,
            ]
        );
    }

    #[Route('/create/recordfromanon/{id}', name: 'record_createfromanon', methods: 'GET|POST', )]
    public function createfromtask(Request $request, int $id): Response
    {
        $task = $this->taskRepository
            ->findOneById($id);
        $form = $this->createForm(TaskType::class, $task, ['method' => 'PUT']);

        $record = new Record();
        $record->setTitle($task->getTitle());
        $record->setText($task->getText());
        $record->setCreatedAt($task->getCreatedAt());
        $record->setCategory($task->getCategory());
        $record->setVisibility(1);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->recordService->save($record);
            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            $this->taskRepository
                ->delete($task);
            $this->addFlash(
                'success',
                $this->translator->trans('message.deleted_successfully')
            );

            return $this->redirectToRoute('record_index'); // Redirect to a list of records
        }

        return $this->render('record/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
