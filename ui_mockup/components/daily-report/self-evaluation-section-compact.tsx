'use client'

import { Button } from '@/components/ui/button'

const ratingLabels = ['Rất kém', 'Kém', 'Bình thường', 'Tốt', 'Rất tốt']

interface SelfEvaluationSectionProps {
  qualityRating: number
  spiritRating: number
  onQualityChange: (rating: number) => void
  onSpiritChange: (rating: number) => void
}

export default function SelfEvaluationSectionCompact({
  qualityRating,
  spiritRating,
  onQualityChange,
  onSpiritChange,
}: SelfEvaluationSectionProps) {
  return (
    <div className="space-y-2">
      {/* Quality Rating */}
      <div>
        <div className="text-xs font-medium text-slate-700 mb-1">Chất lượng</div>
        <div className="flex gap-1">
          {[1, 2, 3, 4, 5].map((rating) => (
            <Button
              key={rating}
              type="button"
              variant={qualityRating === rating ? 'default' : 'outline'}
              size="sm"
              className="flex-1 h-7 text-xs"
              onClick={() => onQualityChange(rating)}
              title={ratingLabels[rating - 1]}
            >
              {rating}
            </Button>
          ))}
        </div>
      </div>

      {/* Spirit Rating */}
      <div>
        <div className="text-xs font-medium text-slate-700 mb-1">Tinh thần</div>
        <div className="flex gap-1">
          {[1, 2, 3, 4, 5].map((rating) => (
            <Button
              key={rating}
              type="button"
              variant={spiritRating === rating ? 'default' : 'outline'}
              size="sm"
              className="flex-1 h-7 text-xs"
              onClick={() => onSpiritChange(rating)}
              title={ratingLabels[rating - 1]}
            >
              {rating}
            </Button>
          ))}
        </div>
      </div>
    </div>
  )
}
