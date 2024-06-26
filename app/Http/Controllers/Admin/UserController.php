<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Repositories\User\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, UserRepository $userRepository) : JsonResponse
    {
        $userQuery = array_merge(
            $request->only(['search', 'filters', 'order_by', 'order', 'per_page', 'page']),
            [
                'with'     => [],
                'where'    => [
                    ['name', '!=', 'admin']
                ],
                'order_by' => 'id',
                'order'    => 'ASC'
            ]
        );

        $users = $userRepository->paginate($userQuery);
        return $this->sendSuccess($users, 'Users retrieved successfully.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, UserRepository $userRepository) : JsonResponse
    {
        try {

            $user = $userRepository->create($request->all());

            return $this->sendSuccess(
                $user,
                'User created successfully.',
                200
            );

        } catch (\Exception $e) {
            return $this->sendError('Something Went Wrong', [$e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, UserRepository $userRepository, $user) : JsonResponse
    {
        try {
            $user = $userRepository->find($user);

            $updated = $userRepository->update($request->all(), $user);

            return $this->sendSuccess(
                $updated,
                'User updated successfully.',
                200
            );
        } catch (\Exception $e) {
            return $this->sendError('Something Went Wrong', [$e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
