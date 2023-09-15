<?php

/**
 * User controller.
 */

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\ChangePasswordType;
use App\Form\Type\UserType;
use App\Service\UserService;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    /**
     * Password hasher.
     */
    private UserPasswordHasherInterface $passwordHasher;
    /**
     * @var UserService
     */
    private $userService;

    /**
     * UserController constructor.
     */
    public function __construct(UserService $userService, UserPasswordHasherInterface $passwordHasher)
    {
        $this->userService = $userService;
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * Index of user.
     */
    #[Route(
        '/{id}/index',
        name: 'user_index',
        methods: 'GET',
    )]
    public function index(Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $pagination = $this->userService->createPaginatedList($page);

        return $this->render(
            'user/index.html.twig',
            ['pagination' => $pagination]
        );
    }

    /**
     * Show user.
     */
    #[Route(
        '/{id}/show',
        name: 'user_show',
        methods: 'GET',
    )]
    public function show(User $user): Response
    {
        return $this->render(
            'user/show.html.twig',
            ['user' => $user]
        );
    }

    /**
     * Edit email.
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    #[Route(
        '/{id}/edit/email',
        name: 'edit_email',
        methods: 'GET|PUT',
    )]
    public function editEmail(Request $request, User $user): Response
    {
        $form = $this->createForm(UserType::class, $user, ['method' => 'PUT']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userService->save($user);

            $id = $user->getId();

            $this->addFlash('success', 'email_updated');
            if ($this->isGranted('ROLE_ADMIN')) {
                $redirect = $this->redirectToRoute('user_index', ['id' => $id]);
            } else {
                $redirect = $this->redirectToRoute('user_show', ['id' => $id]);
            }

            return $redirect;
        }

        return $this->render(
            'user/edit.html.twig',
            [
                'form' => $form->createView(),
                'user' => $user,
            ]
        );
    }

    /**
     * Edit password.
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    #[Route(
        '/{id}/edit/password',
        name: 'edit_password',
        methods: 'GET|PUT',
    )]
    public function editPassword(Request $request, User $user, UserPasswordHasherInterface $passwordHasher): Response
    {
        $form = $this->createForm(ChangePasswordType::class, $user, ['method' => 'PUT']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $this->passwordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            $this->userService->save($user);

            $id = $user->getId();
            $this->addFlash('success', 'password_updated');

            return $this->redirectToRoute('user_index', ['id' => $id]);
        }

        return $this->render(
            'user/editPass.html.twig',
            [
                'form' => $form->createView(),
                'user' => $user,
            ]
        );
    }
}
