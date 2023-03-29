<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register', methods: ['GET', 'POST'])]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $error = null;
        $success = false;

        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $plainPassword = $request->request->get('password');

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Invalid email format';
            } elseif (strlen($plainPassword) < 6) {
                $error = 'Password must be at least 6 characters long';
            } else {
                $encodedPassword = $passwordHasher->hashPassword(new User($email, ''), $plainPassword);

                $usersJsonPath = $this->getParameter('kernel.project_dir') . '/src/Data/users.json';
                $users = json_decode(file_get_contents($usersJsonPath), true);

                $newId = count($users) + 1;

                $userData = [
                    'id' => $newId,
                    'email' => $email,
                    'password' => $encodedPassword,
                ];

                $users[] = $userData;
                file_put_contents($usersJsonPath, json_encode($users));

                $success = true;
            }
        }

        return $this->render('register.html.twig', [
            'success' => $success,
            'error' => $error,
        ]);
    }
}
