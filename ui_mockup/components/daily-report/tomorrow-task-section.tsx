'use client'

import { Plus, Trash2, GripVertical } from 'lucide-react'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Input } from '@/components/ui/input'

interface TomorrowTask {
  id: string
  description: string
}

interface TomorrowTaskSectionProps {
  tasks: TomorrowTask[]
  onAddTask: () => void
  onRemoveTask: (id: string) => void
  onUpdateTask: (id: string, value: string) => void
}

export default function TomorrowTaskSection({
  tasks,
  onAddTask,
  onRemoveTask,
  onUpdateTask,
}: TomorrowTaskSectionProps) {
  return (
    <Card>
      <CardHeader>
        <CardTitle>Task ngày mai</CardTitle>
        <CardDescription>Danh sách các công việc dự kiến cho ngày hôm sau</CardDescription>
      </CardHeader>
      <CardContent>
        <div className="space-y-2">
          {tasks.map((task) => (
            <div key={task.id} className="flex gap-2 items-center p-2 hover:bg-slate-50 rounded-md transition-colors group cursor-move">
              {/* Drag Handle */}
              <div className="flex-shrink-0 text-slate-400 hover:text-slate-600 transition-colors">
                <GripVertical className="w-4 h-4" />
              </div>

              <Input
                placeholder="Mô tả..."
                value={task.description}
                onChange={(e) => onUpdateTask(task.id, e.target.value)}
                className="flex-1 h-9 text-sm"
              />

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
