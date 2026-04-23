'use client'

import { Plus, Trash2, GripVertical } from 'lucide-react'
import { Button } from '@/components/ui/button'
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

export default function TomorrowTaskSectionCompact({
  tasks,
  onAddTask,
  onRemoveTask,
  onUpdateTask,
}: TomorrowTaskSectionProps) {
  return (
    <div className="space-y-1">
      {tasks.map((task) => (
        <div key={task.id} className="flex gap-1.5 items-center p-1.5 hover:bg-slate-100 rounded transition-colors group cursor-move">
          {/* Drag Handle */}
          <div className="flex-shrink-0 text-slate-300 hover:text-slate-500 transition-colors">
            <GripVertical className="w-3 h-3" />
          </div>

          <Input
            placeholder="..."
            value={task.description}
            onChange={(e) => onUpdateTask(task.id, e.target.value)}
            className="flex-1 h-7 text-xs px-2"
          />

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
