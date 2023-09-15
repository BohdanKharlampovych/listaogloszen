<?php
/**
 * Task controller.
 */

namespace App\Controller;

use App\Entity\Task;
use App\Form\Type\TaskType;
use App\Service\RecordServiceInterface;
use App\Service\TaskServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class TaskController.
 */
#[Route('/task')]
class TaskController extends AbstractController
{
    /**
     * Task service.
     */
    private TaskServiceInterface $taskService;

    /**
     * Task service.
     */
    private RecordServiceInterface $recordService;
    /**
     * Translator.
     */
    private TranslatorInterface $translator;

    /**
     * Constructor.
     *
     * @param TaskServiceInterface $taskService Task service
     * @param TranslatorInterface  $translator  Translator
     */
    public function __construct(RecordServiceInterface $recordService, TaskServiceInterface $taskService, TranslatorInterface $translator, Security $security)
    {
        $this->taskService = $taskService;
        $this->translator = $translator;
        $this->security = $security;
        $this->recordService = $recordService;
    }

    /**
     * Index action.
     *
     * @param Request $request HTTP Request
     *
     * @return Response HTTP response
     */
    #[Route(name: 'task_index', methods: 'GET')]
    public function index(Request $request): Response
    {
        $pagination = $this->taskService->getPaginatedList(
            $request->query->getInt('page', 1)
        );

        return $this->render('task/index.html.twig', ['pagination' => $pagination]);
    }

    /**
     * Show action.
     *
     * @param Task $task Task
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{id}',
        name: 'task_show',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET'
    )]
    public function show(Task $task): Response
    {
        return $this->render('task/show.html.twig', ['task' => $task]);
    }

    /**
     * Create action.
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response
     */
    #[Route('/create/task', name: 'task_create', methods: 'GET|POST', )]
    public function create(Request $request): Response
    {
        $task = new Task();
        $form = $this->createForm(
            TaskType::class,
            $task
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $text = $form->get('text')->getData();
            $title = $form->get('title')->getData();
            $task->setText($text);
            $task->setTitle($title);
            $this->taskService->save($task);
            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            return $this->redirectToRoute('record_index');
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * Edit action.
     *
     * @param Request $request HTTP request
     * @param Task    $task    Task entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/edit', name: 'task_edit', requirements: ['id' => '[1-9]\d*'], methods: 'GET|PUT')]
    public function edit(Request $request, Task $task): Response
    {
        $form = $this->createForm(
            TaskType::class,
            $task,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('task_edit', ['id' => $task->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->taskService->save($task);

            $this->addFlash(
                'success',
                $this->translator->trans('message.edited_successfully')
            );

            return $this->redirectToRoute('task_index');
        }

        return $this->render(
            'task/edit.html.twig',
            [
                'form' => $form->createView(),
                'task' => $task,
            ]
        );
    }

    /**
     * Delete action.
     *
     * @param Request $request HTTP request
     * @param Task    $task    Task entity
     *
     * @return Response HTTP response
     */
    #[Route('/task/delete/{id}', name: 'task_delete', requirements: ['id' => '[1-9]\d*'], methods: 'GET|DELETE')]
    public function delete(Request $request, Task $task): Response
    {
        $form = $this->createForm(
            FormType::class,
            $task,
            [
                'method' => 'DELETE',
                'action' => $this->generateUrl('task_delete', ['id' => $task->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->taskService->delete($task);

            $this->addFlash(
                'success',
                $this->translator->trans('message.deleted_successfully')
            );

            return $this->redirectToRoute('task_index');
        }

        return $this->render(
            'task/delete.html.twig',
            [
                'form' => $form->createView(),
                'task' => $task,
            ]
        );
    }
}
