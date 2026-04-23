'use client'

import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'

interface SelfEvaluationSectionProps {
  qualityRating: number
  spiritRating: number
  onQualityChange: (value: number) => void
  onSpiritChange: (value: number) => void
}

const RATING_LABELS = {
  1: 'Rất kém',
  2: 'Kém',
  3: 'Bình thường',
  4: 'Tốt',
  5: 'Rất tốt'
}

const RATING_COLORS = {
  1: 'bg-red-500 hover:bg-red-600',
  2: 'bg-orange-500 hover:bg-orange-600',
  3: 'bg-yellow-500 hover:bg-yellow-600',
  4: 'bg-green-500 hover:bg-green-600',
  5: 'bg-emerald-500 hover:bg-emerald-600'
}

export default function SelfEvaluationSection({
  qualityRating,
  spiritRating,
  onQualityChange,
  onSpiritChange,
}: SelfEvaluationSectionProps) {
  return (
    <Card>
      <CardHeader>
        <CardTitle>Tự đánh giá</CardTitle>
        <CardDescription>Đánh giá chất lượng công việc và tinh thần làm việc</CardDescription>
      </CardHeader>
      <CardContent className="space-y-8">
        {/* Quality Rating */}
        <div>
          <div className="flex items-center justify-between mb-4">
            <label className="text-sm font-semibold text-slate-900">Chất lượng công việc</label>
            <span className="text-sm font-medium text-slate-600">
              {RATING_LABELS[qualityRating as keyof typeof RATING_LABELS]}
            </span>
          </div>
          <div className="flex gap-2">
            {[1, 2, 3, 4, 5].map((rating) => (
              <Button
                key={rating}
                type="button"
                variant={qualityRating === rating ? 'default' : 'outline'}
                className={qualityRating === rating ? `${RATING_COLORS[rating as keyof typeof RATING_COLORS]} text-white border-0` : ''}
                onClick={() => onQualityChange(rating)}
              >
                {rating}
              </Button>
            ))}
          </div>
        </div>

        {/* Spirit Rating */}
        <div>
          <div className="flex items-center justify-between mb-4">
            <label className="text-sm font-semibold text-slate-900">Tinh thần làm việc</label>
            <span className="text-sm font-medium text-slate-600">
              {RATING_LABELS[spiritRating as keyof typeof RATING_LABELS]}
            </span>
          </div>
          <div className="flex gap-2">
            {[1, 2, 3, 4, 5].map((rating) => (
              <Button
                key={rating}
                type="button"
                variant={spiritRating === rating ? 'default' : 'outline'}
                className={spiritRating === rating ? `${RATING_COLORS[rating as keyof typeof RATING_COLORS]} text-white border-0` : ''}
                onClick={() => onSpiritChange(rating)}
              >
                {rating}
              </Button>
            ))}
          </div>
        </div>
      </CardContent>
    </Card>
  )
}
