'use client'

import { useState } from 'react'
import { Plus, Trash2, HistoryIcon } from 'lucide-react'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Textarea } from '@/components/ui/textarea'
import { Switch } from '@/components/ui/switch'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import Image from 'next/image'
import Link from 'next/link'
import TodayTaskSection from '@/components/daily-report/today-task-section-compact'
import TomorrowTaskSection from '@/components/daily-report/tomorrow-task-section-compact'
import SelfEvaluationSection from '@/components/daily-report/self-evaluation-section-compact'

interface TodayTask {
  id: string
  description: string
  progress: number
  expectedDate?: string
}

interface TomorrowTask {
  id: string
  description: string
}

const PROJECTS = [
  'JRR',
  'Primas',
  'Project A',
  'Project B',
  'Project C',
  'Project D',
  'Project E',
  'Project F',
  'Project G',
  'Project H',
]

const PROJECT_LOGOS: Record<string, string> = {
  'JRR': '/logos/jrr.jpg',
  'Primas': '/logos/primas.jpg',
  'Project A': '/logos/project-a.jpg',
  'Project B': '/logos/project-b.jpg',
  'Project C': '/logos/project-c.jpg',
  'Project D': '/logos/project-d.jpg',
  'Project E': '/logos/project-e.jpg',
  'Project F': '/logos/project-f.jpg',
  'Project G': '/logos/project-g.jpg',
  'Project H': '/logos/project-h.jpg',
}

export default function CompactDailyReportPage() {
  const [selectedProject, setSelectedProject] = useState('JRR')
  const [todayTasks, setTodayTasks] = useState<TodayTask[]>([
    { id: '1', description: '', progress: 0 }
  ])
  const [tomorrowTasks, setTomorrowTasks] = useState<TomorrowTask[]>([
    { id: '1', description: '' }
  ])
  const [qualityRating, setQualityRating] = useState(3)
  const [spiritRating, setSpiritRating] = useState(3)
  const [notes, setNotes] = useState('')
  const [submitToGForm, setSubmitToGForm] = useState(true)

  // Today tasks handlers
  const addTodayTask = () => {
    setTodayTasks([...todayTasks, { id: Date.now().toString(), description: '', progress: 0 }])
  }

  const removeTodayTask = (id: string) => {
    if (todayTasks.length > 1) {
      setTodayTasks(todayTasks.filter(task => task.id !== id))
    }
  }

  const updateTodayTask = (id: string, field: keyof TodayTask, value: any) => {
    setTodayTasks(todayTasks.map(task =>
      task.id === id ? { ...task, [field]: value } : task
    ))
  }

  // Tomorrow tasks handlers
  const addTomorrowTask = () => {
    setTomorrowTasks([...tomorrowTasks, { id: Date.now().toString(), description: '' }])
  }

  const removeTomorrowTask = (id: string) => {
    if (tomorrowTasks.length > 1) {
      setTomorrowTasks(tomorrowTasks.filter(task => task.id !== id))
    }
  }

  const updateTomorrowTask = (id: string, value: string) => {
    setTomorrowTasks(tomorrowTasks.map(task =>
      task.id === id ? { ...task, description: value } : task
    ))
  }

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    
    const reportData = {
      project: selectedProject,
      date: new Date().toLocaleDateString('vi-VN'),
      todayTasks: todayTasks.filter(t => t.description.trim()),
      tomorrowTasks: tomorrowTasks.filter(t => t.description.trim()),
      qualityRating,
      spiritRating,
      notes,
      submittedAt: new Date().toISOString()
    }

    console.log('Report submitted:', reportData)

    if (submitToGForm) {
      console.log('Sending to Google Form...')
    }

    alert('Daily report submitted successfully!')
  }

  return (
    <div className="min-h-screen bg-slate-50">
      <div className="max-w-5xl mx-auto p-3 md:p-4">
        {/* Header */}
        <div className="flex items-center justify-between mb-4">
          <div>
            <h1 className="text-2xl md:text-3xl font-bold text-slate-900">
              Daily Report
            </h1>
            <p className="text-xs md:text-sm text-slate-600 mt-0.5">
              {new Date().toLocaleDateString('vi-VN')}
            </p>
          </div>
          <Link href="/logwork-history">
            <Button variant="outline" size="sm" className="gap-1 text-xs">
              <HistoryIcon className="w-3 h-3" />
              Lịch sử
            </Button>
          </Link>
        </div>

        {/* Main Form */}
        <form onSubmit={handleSubmit} className="space-y-3">
          {/* Project Selection */}
          <Card className="border-slate-200">
            <CardContent className="p-3">
              <div className="flex items-center gap-2">
                <div className="w-12 h-12 flex-shrink-0">
                  <Image
                    src={PROJECT_LOGOS[selectedProject]}
                    alt={selectedProject}
                    width={48}
                    height={48}
                    className="w-full h-full object-cover rounded"
                  />
                </div>
                <Select value={selectedProject} onValueChange={setSelectedProject}>
                  <SelectTrigger className="flex-1 h-8 text-xs">
                    <SelectValue />
                  </SelectTrigger>
                  <SelectContent>
                    {PROJECTS.map((project) => (
                      <SelectItem key={project} value={project} className="text-xs">
                        {project}
                      </SelectItem>
                    ))}
                  </SelectContent>
                </Select>
              </div>
            </CardContent>
          </Card>

          <div className="grid grid-cols-1 lg:grid-cols-2 gap-3">
            {/* Today's Tasks */}
            <Card className="border-slate-200 lg:col-span-2">
              <CardHeader className="p-3 pb-2">
                <CardTitle className="text-sm">Công việc hôm nay</CardTitle>
              </CardHeader>
              <CardContent className="p-3 pt-1">
                <TodayTaskSection
                  tasks={todayTasks}
                  onAddTask={addTodayTask}
                  onRemoveTask={removeTodayTask}
                  onUpdateTask={updateTodayTask}
                />
              </CardContent>
            </Card>

            {/* Tomorrow's Tasks */}
            <Card className="border-slate-200 lg:col-span-2">
              <CardHeader className="p-3 pb-2">
                <CardTitle className="text-sm">Công việc ngày mai</CardTitle>
              </CardHeader>
              <CardContent className="p-3 pt-1">
                <TomorrowTaskSection
                  tasks={tomorrowTasks}
                  onAddTask={addTomorrowTask}
                  onRemoveTask={removeTomorrowTask}
                  onUpdateTask={updateTomorrowTask}
                />
              </CardContent>
            </Card>

            {/* Self Evaluation */}
            <Card className="border-slate-200">
              <CardHeader className="p-3 pb-2">
                <CardTitle className="text-sm">Đánh giá</CardTitle>
              </CardHeader>
              <CardContent className="p-3 pt-1">
                <SelfEvaluationSection
                  qualityRating={qualityRating}
                  spiritRating={spiritRating}
                  onQualityChange={setQualityRating}
                  onSpiritChange={setSpiritRating}
                />
              </CardContent>
            </Card>

            {/* Notes + Submit */}
            <div className="flex flex-col gap-3">
              {/* Notes */}
              <Card className="border-slate-200 flex-1">
                <CardHeader className="p-3 pb-2">
                  <CardTitle className="text-sm">Ghi chú</CardTitle>
                </CardHeader>
                <CardContent className="p-3 pt-1">
                  <Textarea
                    placeholder="Thêm ghi chú..."
                    value={notes}
                    onChange={(e) => setNotes(e.target.value)}
                    className="min-h-16 text-xs"
                  />
                </CardContent>
              </Card>

              {/* Google Form Integration */}
              <Card className="border-slate-200">
                <CardContent className="p-3 flex items-center justify-between">
                  <div className="text-xs font-medium text-slate-700">Google Form</div>
                  <Switch
                    checked={submitToGForm}
                    onCheckedChange={setSubmitToGForm}
                    className="scale-75"
                  />
                </CardContent>
              </Card>
            </div>
          </div>

          {/* Submit Button */}
          <Button
            type="submit"
            className="w-full h-8 text-xs"
          >
            Gửi Report
          </Button>
        </form>
      </div>
    </div>
  )
}
