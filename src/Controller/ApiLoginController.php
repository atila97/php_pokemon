<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ApiLoginController extends AbstractController
{
    #[Route('/api/login', name: 'app_api_login')]
    public function index(): JsonResponse
    {
        $user = $this->getUser();
        if (empty($user)) {
            return $this->json([
                'message' => 'missing credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $this->json([
            'user'  => $user->getRoles(),
        ]);
    }

    #[Route('/api/register', name: 'app_api_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $errors = [];
        if (false === $this->validateEmail($data['email'] ?? "")) {
            $errors[] =  "L'e-mail n'est pas valide.";
        }
        if (false === $this->validatePassword($data['password'] ?? "")) {
            $errors[] =  "Le mot de passe doit comporter au minimum 8 caractères, une majuscule, une minuscule et un caractère spécial ,;:?./@#'{[]}()$*%=+";
        }

        if (count($errors) >= 1) {
            return $this->json([ "errors" => $errors], Response::HTTP_UNAUTHORIZED);
        }

        if (null !== $entityManager->getRepository(User::class)->findOneBy(["email" => $data["email"]])) {
            return $this->json([ "errors" => ["Il existe déjà un compte avec cet email"]
            ], Response::HTTP_UNAUTHORIZED);
        }

        $user = new User();
        $user->setEmail($data['email']);
        $user->setPassword(
            $userPasswordHasher->hashPassword(
                $user,
                $data['password']
            )
        );
        $entityManager->persist($user);
        $entityManager->flush();
        return $this->json([
            'user'  => $user->getRoles(),
        ]);
    }

    function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    function validatePassword($password) {
        $minLength = 8;
        $hasSpecialChar = preg_match('/[!@#$%^&*()_+\-=[\]{};\':"\\|,.<>\/?]+/', $password);
        return strlen($password) >= $minLength && $hasSpecialChar;
    }
}
