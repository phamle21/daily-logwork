'use client'

import { Plus, Trash2, GripVertical } from 'lucide-react'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
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

export default function TodayTaskSection({
  tasks,
  onAddTask,
  onRemoveTask,
  onUpdateTask,
}: TodayTaskSectionProps) {
  const progressOptions = Array.from({ length: 11 }, (_, i) => i * 10)

  return (
    <Card>
      <CardHeader>
        <CardTitle>Task ngày hôm nay</CardTitle>
        <CardDescription>Danh sách các công việc hoàn thành hoặc đang tiến hành</CardDescription>
      </CardHeader>
      <CardContent>
        <div className="space-y-2">
          {tasks.map((task) => (
            <div key={task.id} className="flex flex-col sm:flex-row gap-2 sm:gap-3 items-start sm:items-center p-2 hover:bg-slate-50 rounded-md transition-colors group cursor-move">
              {/* Drag Handle */}
              <div className="flex-shrink-0 text-slate-400 hover:text-slate-600 transition-colors mt-1 sm:mt-0">
                <GripVertical className="w-4 h-4" />
              </div>

              {/* Description */}
              <Input
                placeholder="Mô tả..."
                value={task.description}
                onChange={(e) => onUpdateTask(task.id, 'description', e.target.value)}
                className="flex-1 h-9 text-sm"
              />

              {/* Progress */}
              <Select
                value={task.progress.toString()}
                onValueChange={(value) => onUpdateTask(task.id, 'progress', parseInt(value))}
              >
                <SelectTrigger className="w-20 h-9 text-sm">
                  <SelectValue placeholder={`${task.progress}%`} />
                </SelectTrigger>
                <SelectContent>
                  {progressOptions.map((option) => (
                    <SelectItem key={option} value={option.toString()}>
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
                  className="w-32 h-9 text-sm"
                />
              )}

              {/* Delete Button */}
              {tasks.length > 1 && (
                <Button
                  type="button"
                  variant="ghost"
                  size="icon"
                  className="h-9 w-9 text-red-600 hover:text-red-700 hover:bg-red-50 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0"
                  onClick={() => onRemoveTask(task.id)}
                >
                  <Trash2 className="w-4 h-4" />
                </Button>
              )}
            </div>
          ))}

          {/* Add More Button */}
          <Button
            type="button"
            variant="outline"
            size="sm"
            className="w-full mt-2"
            onClick={onAddTask}
          >
            <Plus className="w-3 h-3 mr-1" />
            Thêm
          </Button>
        </div>
      </CardContent>
    </Card>
  )
}
