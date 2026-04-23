'use client'

import { Plus, Trash2, GripVertical } from 'lucide-react'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'

interface TodayTask {
  id: string
  description: string
  progress: number
  expectedDate?: string
}

interface TodayTaskSectionProps {
  tasks: TodayTask[]
  onAddTask: () => void
  onRemoveTask: (id: string) => void
  onUpdateTask: (id: string, field: keyof TodayTask, value: any) => void
}

const progressOptions = [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100]

export default function TodayTaskSectionCompact({
  tasks,
  onAddTask,
  onRemoveTask,
  onUpdateTask,
}: TodayTaskSectionProps) {
  return (
    <div className="space-y-1">
      {tasks.map((task) => (
        <div key={task.id} className="flex gap-1.5 items-center p-1.5 hover:bg-slate-100 rounded transition-colors group cursor-move">
          {/* Drag Handle */}
          <div className="flex-shrink-0 text-slate-300 hover:text-slate-500 transition-colors">
            <GripVertical className="w-3 h-3" />
          </div>

          {/* Description */}
          <Input
            placeholder="..."
            value={task.description}
            onChange={(e) => onUpdateTask(task.id, 'description', e.target.value)}
            className="flex-1 h-7 text-xs px-2"
          />

          {/* Progress */}
          <Select
            value={task.progress.toString()}
            onValueChange={(value) => onUpdateTask(task.id, 'progress', parseInt(value))}
          >
            <SelectTrigger className="w-14 h-7 text-xs px-1">
              <SelectValue placeholder={`${task.progress}%`} />
            </SelectTrigger>
            <SelectContent className="min-w-0">
              {progressOptions.map((option) => (
                <SelectItem key={option} value={option.toString()} className="text-xs">
                  {option}%
                </SelectItem>
              ))}
            </SelectContent>
          </Select>

          {/* Expected Date (show only if progress < 100) */}
          {task.progress < 100 && (
            <Input
              type="date"
              value={task.expectedDate || ''}
              onChange={(e) => onUpdateTask(task.id, 'expectedDate', e.target.value)}
              className="w-24 h-7 text-xs px-2"
            />
          )}

          {/* Delete Button */}
          {tasks.length > 1 && (
            <Button
              type="button"
              variant="ghost"
              size="icon"
              className="h-7 w-7 text-red-600 hover:text-red-700 hover:bg-red-50 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0"
              onClick={() => onRemoveTask(task.id)}
            >
              <Trash2 className="w-3 h-3" />
            </Button>
          )}
        </div>
      ))}

      {/* Add More Button */}
      <Button
        type="button"
        variant="ghost"
        size="sm"
        className="w-full h-6 text-xs mt-1"
        onClick={onAddTask}
      >
        <Plus className="w-3 h-3 mr-0.5" />
        Thêm
      </Button>
    </div>
  )
}
