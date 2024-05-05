<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Task;

#[Route('/api', name: 'api_')]
class TaskController extends AbstractController
{

    #[Route('/tasks', name: 'task_create', methods:['post'])]
    public function create(ManagerRegistry $doctrine, Request $request): JsonResponse
    {
        $entityManager = $doctrine->getManager();

        $requestData = json_decode($request->getContent(), true);

        // Check if description field exists and is not null
        if (!isset($requestData['description']) || $requestData['description'] === null) {
            // Handle missing or null description
            return new JsonResponse(['error' => 'Description field is missing or null'], JsonResponse::HTTP_BAD_REQUEST);
        }

        if (!isset($requestData['priority']) || $requestData['priority'] === null) {
            // Handle missing or null description
            return new JsonResponse(['error' => 'Priority field is missing or null'], JsonResponse::HTTP_BAD_REQUEST);
        }

        if (!isset($requestData['deadline']) || $requestData['deadline'] === null) {
            // Handle missing or null description
            return new JsonResponse(['error' => 'Deadline field is missing or null'], JsonResponse::HTTP_BAD_REQUEST);
        }

        if (!isset($requestData['completed']) || $requestData['completed'] === null) {
            // Handle missing or null description
            return new JsonResponse(['error' => 'Completed field is missing or null'], JsonResponse::HTTP_BAD_REQUEST);
        }

        
        // echo $requestData;
        $task = new Task();
        $task->setDescription($requestData['description']);
        $task->setPriority($requestData['priority']);
        $task->setDeadline($requestData['deadline']);
        $task->setCompleted($requestData['completed']);

        $entityManager->persist($task);
        $entityManager->flush();

        $data = [
            'id' => $task->getId(),
            'description' => $task->getDescription(),
            'priority' => $task->getPriority(),
            'deadline' => $task->getDeadline(),
            'completed' => $task->isCompleted(),
        ];

        
        return $this->json($data);
    }

    #[Route('/tasks', name: 'tasks_index', methods:['GET'])]
    public function index(ManagerRegistry $doctrine): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        
        // Assuming you have a repository for the Task entity
        $taskRepository = $entityManager->getRepository(Task::class);
        
        // Create a query builder
        $queryBuilder = $taskRepository->createQueryBuilder('t');
        
        // Order tasks by completed field in ascending order and id in descending order
        $tasks = $queryBuilder
            ->orderBy('t.completed', 'ASC') // Orders by completed field in ascending order
            ->addOrderBy('t.id', 'DESC') // Then orders by id in descending order
            ->getQuery()
            ->getResult();
        
        // Prepare data to return
        $data = [];
        
        foreach ($tasks as $task) {
            $data[] = [
                'id' => $task->getId(),
                'description' => $task->getDescription(),
                'priority' => $task->getPriority(),
                'deadline' => $task->getDeadline(),
                'completed' => $task->isCompleted(),
            ];
        }
        
        return $this->json($data);
    }

    #[Route('/tasks/{id}', name: 'task_update', methods:['PUT', 'PATCH'])]
    public function update(ManagerRegistry $doctrine, Request $request,int $id): JsonResponse
    {
        $entityManager = $doctrine->getManager();

        // Find the task by its ID
        $task = $entityManager->getRepository(Task::class)->find($id);

        // If task with the given ID doesn't exist, return an error response
        if (!$task) {
            return new JsonResponse(['error' => 'Task not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Decode the JSON data from the request
        $requestData = json_decode($request->getContent(), true);

        // Check if description field exists and is not null
        if (!isset($requestData['description']) || $requestData['description'] === null) {
            // Handle missing or null description
            return new JsonResponse(['error' => 'Description field is missing or null'], JsonResponse::HTTP_BAD_REQUEST);
        }

        if (!isset($requestData['priority']) || $requestData['priority'] === null) {
            // Handle missing or null description
            return new JsonResponse(['error' => 'Priority field is missing or null'], JsonResponse::HTTP_BAD_REQUEST);
        }

        if (!isset($requestData['deadline']) || $requestData['deadline'] === null) {
            // Handle missing or null description
            return new JsonResponse(['error' => 'Deadline field is missing or null'], JsonResponse::HTTP_BAD_REQUEST);
        }

        if (!isset($requestData['completed']) || $requestData['completed'] === null) {
            // Handle missing or null description
            return new JsonResponse(['error' => 'Completed field is missing or null'], JsonResponse::HTTP_BAD_REQUEST);
        }


        // Update the task properties if they are provided in the request data
        if (isset($requestData['description'])) {
            $task->setDescription($requestData['description']);
        }
        if (isset($requestData['priority'])) {
            $task->setPriority($requestData['priority']);
        }
        if (isset($requestData['deadline'])) {
            $task->setDeadline($requestData['deadline']);
        }
        if (isset($requestData['completed'])) {
            $task->setCompleted($requestData['completed']);
        }

        // Persist the changes to the database
        $entityManager->persist($task);
        $entityManager->flush();

        // Prepare and return the updated task data
        $data = [
            'id' => $task->getId(),
            'description' => $task->getDescription(),
            'priority' => $task->getPriority(),
            'deadline' => $task->getDeadline(),
            'completed' => $task->isCompleted(),
        ];

        return $this->json($data);
    }

    #[Route('/tasks/{id}', name: 'task_delete', methods:['DELETE'])]
    public function delete($id, ManagerRegistry $doctrine): JsonResponse
    {
        $entityManager = $doctrine->getManager();

        // Find the task by its ID
        $task = $entityManager->getRepository(Task::class)->find($id);

        // If task with the given ID doesn't exist, return an error response
        if (!$task) {
            return new JsonResponse(['error' => 'Task not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Remove the task
        $entityManager->remove($task);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Task deleted successfully']);
    }

}
