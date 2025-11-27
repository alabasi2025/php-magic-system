<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property \App\Models\Project $resource
 */
class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Calculate the percentage of completed tasks
        // Assuming the Project model has a 'tasks' relationship loaded
        $totalTasks = $this->resource->tasks->count();
        $completedTasks = $this->resource->tasks->where('status', 'completed')->count();
        $completionPercentage = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 2) : 0.00;

        // Determine the project's overall status based on task completion and due date
        // If the project is past its due date AND not 100% complete, it's 'Overdue'
        $isOverdue = $this->resource->due_date && $this->resource->due_date->isPast() && $completionPercentage < 100;
        $statusIndicator = $isOverdue ? 'Overdue' : $this->resource->status;

        return [
            // Primary identification fields
            'id' => $this->resource->id,

            // Core project details
            'name' => $this->resource->name,
            'description' => $this->resource->description,
            'status' => $statusIndicator, // Use the computed status
            'priority' => $this->resource->priority,

            // Date fields
            'start_date' => $this->resource->start_date ? $this->resource->start_date->format('Y-m-d') : null,
            'due_date' => $this->resource->due_date ? $this->resource->due_date->format('Y-m-d') : null,
            'created_at' => $this->resource->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->resource->updated_at->format('Y-m-d H:i:s'),

            // Computed fields
            'total_tasks' => $totalTasks,
            'completed_tasks' => $completedTasks,
            'completion_percentage' => $completionPercentage,
            'is_overdue' => $isOverdue,

            // Relationships (Conditional loading to prevent N+1 issues)
            // Assumes TaskResource is defined and available
            'tasks' => TaskResource::collection($this->whenLoaded('tasks')),
        ];
    }
}