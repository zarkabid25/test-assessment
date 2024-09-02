<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Task",
 *     type="object",
 *     title="Task",
 *     required={"title", "status", "user_id"},
 *     properties={
 *         @OA\Property(property="id", type="integer", description="Task ID"),
 *         @OA\Property(property="title", type="string", description="Task title"),
 *         @OA\Property(property="description", type="string", description="Task description"),
 *         @OA\Property(property="status", type="string", enum={"Pending", "Completed"}, description="Task status"),
 *         @OA\Property(property="user_id", type="integer", description="ID of the user assigned to the task"),
 *         @OA\Property(property="created_at", type="string", format="date-time", description="Creation timestamp"),
 *         @OA\Property(property="updated_at", type="string", format="date-time", description="Last update timestamp")
 *     }
 * )
 */
class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'status',
        'user_id',
    ];
}
