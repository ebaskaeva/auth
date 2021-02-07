<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
    /**
	 * @Route("/api")
	 */
{
    public function __construct(UserRepository $userRepository, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
    }
	/*
    #[Route('/user', name: 'user')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }*/
    
    /**
	 * @Route("/users", name="users", methods={"GET"})
	 */
	public function getAll(): JsonResponse
	{
		$users = $this->userRepository->findAll();
		$data = [];

		foreach ($users as $user) {
			$data[] = [
			'id' => $user->getId(),
			'email' => $user->getEmail(),
			'roles' => $user->getRoles(),
			'name' => $user->getName(),
		];
		}
		return new JsonResponse($data, Response::HTTP_OK);
	}
    
    /**
	 * @Route("/users/{id}", name="user_by_id", methods={"GET"})
	 */
	public function get($id): JsonResponse
	{
		$user = $this->userRepository->findOneBy(['id' => $id]);
		
		if (empty($user)) {
			return new JsonResponse("User not found", Response::HTTP_NOT_FOUND);
		}

		$data = [
			'id' => $user->getId(),
			'email' => $user->getEmail(),
			'roles' => $user->getRoles(),
			'name' => $user->getName()
		];
		
		return new JsonResponse($data, Response::HTTP_OK);
	}
     /**
     * @Route("/users", name="add_user", methods={"POST"})
     */
    public function add(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (empty($data['email']) || empty($data['name']) || empty($data['password'])) {
			return new JsonResponse(
			['status' => 'Expecting mandatory parameters!
			{"email": "email", "roles":"roles", "name": "name", "password"}'], Response::HTTP_NOT_ACCEPTABLE);
        }

        $user = new User();
        $user->setEmail($data['email']);
        $user->setRoles([$data['roles']]);
        $user->setName($data['name']);
        $user->setPassword($this->passwordEncoder->encodePassword($user, $data['password']));
        
		$em = $this->getDoctrine()->getManager();
		$em->persist($user);
        try {
			$em->flush();
        } catch (Exception $e) {
            return new JsonResponse($e->getData(), Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse(['status' => 'User created!'], Response::HTTP_CREATED);
    }
    /**
	 * @Route("/users/{id}", name="update_user", methods={"PUT"})
	 */
	public function update($id, Request $request): JsonResponse
	{
		$user = $this->userRepository->findOneBy(['id' => $id]);

		if (empty($user)) {
			return new JsonResponse("User not found", Response::HTTP_NOT_FOUND);
		}
		
		$data = json_decode($request->getContent(), true);

		empty($data['name']) ? true : $user->setName($data['name']);
		empty($data['roles']) ? true : $user->setRoles([$data['roles']]);
        empty($data['password']) ? true : $user->setPassword($this->passwordEncoder->encodePassword($user, $data['password']));

		$em = $this->getDoctrine()->getManager();
		$em->persist($user);
        try {
			$em->flush();
        } catch (Exception $e) {
            return new JsonResponse($e->getData(), Response::HTTP_BAD_REQUEST);
        }

		return new JsonResponse($user->toArray(), Response::HTTP_OK);
	}
    /**
	 * @Route("/users/{id}", name="delete_user", methods={"DELETE"})
	 */
	public function delete($id): JsonResponse
	{
		$user = $this->userRepository->findOneBy(['id' => $id]);
		
		if (empty($user)) {
			return new JsonResponse("User not found", Response::HTTP_NOT_FOUND);
		}

		$em = $this->getDoctrine()->getManager();
		$em->remove($user);
        try {
			$em->flush();
        } catch (Exception $e) {
            return new JsonResponse($e->getData(), Response::HTTP_BAD_REQUEST);
        }

		return new JsonResponse("Deleted successfully", Response::HTTP_OK);
	}
}
