<?php

namespace App\Controller;

use App\Entity\Category;
use App\Service\Entity\CategoryService;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Parameter;
use Nelmio\ApiDocBundle\Annotation\Property;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{

    /**
     * Creates new category
     *
     * @Route("/api/category", name="create_category", methods={"POST"})
     *
     * @param Request $request
     * @param CategoryService $categoryService
     * @return JsonResponse
     * @throws \Exception
     *
     * @OA\RequestBody(
     *     @OA\JsonContent(ref=@Model(type=Category::class, groups={"category_write"}))
     * )
     * @OA\Response(
     *     response=201,
     *     description="Category created",
     *     @Model(type=Category::class, groups={"category_read"})
     * )
     * @OA\Tag(name="Store")
     */
    public function createCategory(Request $request, CategoryService $categoryService)
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data)) {
            return $this->json(['errors' => ['Invalid JSON provided.']], JsonResponse::HTTP_BAD_REQUEST);
        }

        if ($categoryService->create($data)->save()) {
            return $this->json(
                ['category' => $categoryService->getEntity()],
                JsonResponse::HTTP_CREATED,
                [],
                ['groups' => ['category_read']]
            );
        }

        return $this->json(['errors' => $categoryService->getErrors()], JsonResponse::HTTP_BAD_REQUEST);
    }

    /**
     * Updates a category
     *
     * @Route("/api/category/{id}", name="update_category", methods={"PUT"})
     *
     * @param int $id
     * @param Request $request
     * @param CategoryService $categoryService
     * @return JsonResponse
     * @throws \Exception
     *
     * @OA\RequestBody(
     *     @OA\JsonContent(ref=@Model(type=Category::class, groups={"category_write"}))
     * )
     * @OA\Response(
     *     response=200,
     *     description="Category updated",
     *     @Model(type=Category::class, groups={"category_read"})
     * )
     * @OA\Tag(name="Store")
     */
    public function updateCategory(int $id, Request $request, CategoryService $categoryService)
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data)) {
            return $this->json(['errors' => ['Invalid JSON provided.']], JsonResponse::HTTP_BAD_REQUEST);
        }

        $category = $categoryService->find($id);

        if (!isset($category)) {
            return $this->json(['errors' => ['Category not found.']], JsonResponse::HTTP_BAD_REQUEST);
        }

        if ($categoryService->update($category, $data)->save()) {
            return $this->json(
                ['category' => $categoryService->getEntity()],
                JsonResponse::HTTP_OK,
                [],
                ['groups' => ['category_read']]
            );
        }

        return $this->json(['errors' => $categoryService->getErrors()], JsonResponse::HTTP_BAD_REQUEST);
    }

    /**
     * Deletes a category
     *
     * @Route("/api/category/{id}", name="delete_category", methods={"DELETE"})
     *
     * @param int $id
     * @param CategoryService $categoryService
     * @return JsonResponse
     *
     * @OA\Response(
     *     response=204,
     *     description="Category removed"
     * )
     * @OA\Tag(name="Store")
     */
    public function deleteCategory(int $id, CategoryService $categoryService)
    {
        $category = $categoryService->find($id);

        if (!isset($category)) {
            return $this->json(['errors' => ['Category not found.']], JsonResponse::HTTP_BAD_REQUEST);
        }

        if ($categoryService->delete($category)) {
            return $this->json(null, JsonResponse::HTTP_NO_CONTENT);
        }

        return $this->json(
            ['errors' => $categoryService->getErrors()],
            JsonResponse::HTTP_BAD_REQUEST
        );
    }
}