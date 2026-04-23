'use client'

import { ArrowLeft, Calendar, BarChart3 } from 'lucide-react'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import Link from 'next/link'

interface LogworkReport {
  date: string
  tasksCompleted: number
  averageQuality: number
  averageSpirit: number
  totalTasks: number
}

// Mock data - In a real app, this would come from a database
const mockLogworkData: LogworkReport[] = [
  {
    date: '2024-01-19',
    tasksCompleted: 5,
    averageQuality: 4,
    averageSpirit: 4,
    totalTasks: 8
  },
  {
    date: '2024-01-18',
    tasksCompleted: 4,
    averageQuality: 3,
    averageSpirit: 3,
    totalTasks: 7
  },
  {
    date: '2024-01-17',
    tasksCompleted: 6,
    averageQuality: 5,
    averageSpirit: 4,
    totalTasks: 6
  },
  {
    date: '2024-01-16',
    tasksCompleted: 3,
    averageQuality: 3,
    averageSpirit: 2,
    totalTasks: 6
  },
  {
    date: '2024-01-15',
    tasksCompleted: 5,
    averageQuality: 4,
    averageSpirit: 4,
    totalTasks: 8
  },
]

const getRatingColor = (rating: number) => {
  if (rating >= 4.5) return 'text-emerald-600'
  if (rating >= 3.5) return 'text-green-600'
  if (rating >= 2.5) return 'text-yellow-600'
  return 'text-red-600'
}

const getRatingBgColor = (rating: number) => {
  if (rating >= 4.5) return 'bg-emerald-50'
  if (rating >= 3.5) return 'bg-green-50'
  if (rating >= 2.5) return 'bg-yellow-50'
  return 'bg-red-50'
}

export default function LogworkHistoryPage() {
  return (
    <div className="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100">
      <div className="max-w-4xl mx-auto p-4 md:p-8">
        {/* Header */}
        <div className="mb-8">
          <Link href="/">
            <Button variant="ghost" className="gap-2 mb-6">
              <ArrowLeft className="w-4 h-4" />
              Quay lại
            </Button>
          </Link>
          <h1 className="text-3xl md:text-4xl font-bold text-slate-900">
            Lịch sử Logwork
          </h1>
          <p className="text-slate-600 mt-2">
            Xem tất cả các daily report đã gửi
          </p>
        </div>

        {/* Summary Stats */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
          <Card>
            <CardHeader className="pb-3">
              <CardTitle className="text-sm font-medium text-slate-600">
                Tổng Reports
              </CardTitle>
            </CardHeader>
            <CardContent>
              <div className="text-3xl font-bold text-slate-900">
                {mockLogworkData.length}
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader className="pb-3">
              <CardTitle className="text-sm font-medium text-slate-600">
                Avg. Chất lượng
              </CardTitle>
            </CardHeader>
            <CardContent>
              <div className={`text-3xl font-bold ${getRatingColor(4)}`}>
                4.0
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader className="pb-3">
              <CardTitle className="text-sm font-medium text-slate-600">
                Avg. Tinh thần
              </CardTitle>
            </CardHeader>
            <CardContent>
              <div className={`text-3xl font-bold ${getRatingColor(3.6)}`}>
                3.6
              </div>
            </CardContent>
          </Card>
        </div>

        {/* Reports List */}
        <Card>
          <CardHeader>
            <CardTitle>Daily Reports</CardTitle>
            <CardDescription>Danh sách các report được gửi</CardDescription>
          </CardHeader>
          <CardContent>
            <div className="space-y-3">
              {mockLogworkData.map((report) => (
                <div
                  key={report.date}
                  className={`p-4 rounded-lg border transition-colors hover:border-slate-300 cursor-pointer ${getRatingBgColor(report.averageQuality)}`}
                >
                  <div className="flex items-center justify-between gap-4">
                    {/* Date */}
                    <div className="flex items-center gap-3 flex-1">
                      <Calendar className="w-5 h-5 text-slate-400" />
                      <div>
                        <p className="font-medium text-slate-900">
                          {new Date(report.date).toLocaleDateString('vi-VN', {
                            weekday: 'long',
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric'
                          })}
                        </p>
                        <p className="text-sm text-slate-600">
                          {report.tasksCompleted}/{report.totalTasks} tasks completed
                        </p>
                      </div>
                    </div>

                    {/* Ratings */}
                    <div className="flex items-center gap-6">
                      <div className="text-center">
                        <p className="text-xs text-slate-600 mb-1">Chất lượng</p>
                        <p className={`text-lg font-bold ${getRatingColor(report.averageQuality)}`}>
                          {report.averageQuality.toFixed(1)}
                        </p>
                      </div>
                      <div className="text-center">
                        <p className="text-xs text-slate-600 mb-1">Tinh thần</p>
                        <p className={`text-lg font-bold ${getRatingColor(report.averageSpirit)}`}>
                          {report.averageSpirit.toFixed(1)}
                        </p>
                      </div>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          </CardContent>
        </Card>

        {/* Stats Chart Area */}
        <Card className="mt-8">
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <BarChart3 className="w-5 h-5" />
              Thống kê
            </CardTitle>
            <CardDescription>Biểu đồ hiệu suất</CardDescription>
          </CardHeader>
          <CardContent>
            <div className="h-64 flex items-center justify-center bg-slate-50 rounded-lg border border-dashed border-slate-300">
              <p className="text-slate-500">
                Biểu đồ sẽ được thêm vào trong tương lai
              </p>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  )
}
