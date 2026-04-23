'use client'

import { useState } from 'react'
import { Plus, Trash2, HistoryIcon } from 'lucide-react'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
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
import TodayTaskSection from '@/components/daily-report/today-task-section'
import TomorrowTaskSection from '@/components/daily-report/tomorrow-task-section'
import SelfEvaluationSection from '@/components/daily-report/self-evaluation-section'

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

export default function DailyReportPage() {
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

    // TODO: Send to Google Form if submitToGForm is true
    if (submitToGForm) {
      console.log('Sending to Google Form...')
    }

    // Reset form
    alert('Daily report submitted successfully!')
  }

  return (
    <div className="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100">
      <div className="max-w-4xl mx-auto p-4 md:p-8">
        {/* Header */}
        <div className="flex items-center justify-between mb-8">
          <div>
            <h1 className="text-3xl md:text-4xl font-bold text-slate-900">
              Daily Report
            </h1>
            <p className="text-slate-600 mt-2">
              {new Date().toLocaleDateString('vi-VN', { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
              })}
            </p>
          </div>
          <Link href="/logwork-history">
            <Button variant="outline" className="gap-2">
              <HistoryIcon className="w-4 h-4" />
              Lịch sử
            </Button>
          </Link>
        </div>

        {/* Main Form */}
        <form onSubmit={handleSubmit} className="space-y-6">
          {/* Project Selection */}
          <Card>
            <CardHeader>
              <CardTitle>Chọn dự án</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="flex items-center gap-4">
                <div className="w-16 h-16 flex-shrink-0">
                  <Image
                    src={PROJECT_LOGOS[selectedProject]}
                    alt={selectedProject}
                    width={64}
                    height={64}
                    className="w-full h-full object-cover rounded-lg"
                  />
                </div>
                <Select value={selectedProject} onValueChange={setSelectedProject}>
                  <SelectTrigger className="flex-1">
                    <SelectValue />
                  </SelectTrigger>
                  <SelectContent>
                    {PROJECTS.map((project) => (
                      <SelectItem key={project} value={project}>
                        {project}
                      </SelectItem>
                    ))}
                  </SelectContent>
                </Select>
              </div>
            </CardContent>
          </Card>

          {/* Today's Tasks */}
          <TodayTaskSection
            tasks={todayTasks}
            onAddTask={addTodayTask}
            onRemoveTask={removeTodayTask}
            onUpdateTask={updateTodayTask}
          />

          {/* Tomorrow's Tasks */}
          <TomorrowTaskSection
            tasks={tomorrowTasks}
            onAddTask={addTomorrowTask}
            onRemoveTask={removeTomorrowTask}
            onUpdateTask={updateTomorrowTask}
          />

          {/* Self Evaluation */}
          <SelfEvaluationSection
            qualityRating={qualityRating}
            spiritRating={spiritRating}
            onQualityChange={setQualityRating}
            onSpiritChange={setSpiritRating}
          />

          {/* Notes */}
          <Card>
            <CardHeader>
              <CardTitle>Ghi chú</CardTitle>
              <CardDescription>Tùy chọn</CardDescription>
            </CardHeader>
            <CardContent>
              <Textarea
                placeholder="Thêm ghi chú hoặc nhận xét thêm..."
                value={notes}
                onChange={(e) => setNotes(e.target.value)}
                className="min-h-24"
              />
            </CardContent>
          </Card>

          {/* Google Form Integration */}
          <Card>
            <CardHeader>
              <div className="flex items-center justify-between">
                <div>
                  <CardTitle>Google Form Integration</CardTitle>
                  <CardDescription>Gửi report tới Google Form</CardDescription>
                </div>
                <Switch
                  checked={submitToGForm}
                  onCheckedChange={setSubmitToGForm}
                />
              </div>
            </CardHeader>
          </Card>

          {/* Submit Button */}
          <Button
            type="submit"
            size="lg"
            className="w-full"
          >
            Gửi Daily Report
          </Button>
        </form>
      </div>
    </div>
  )
}
