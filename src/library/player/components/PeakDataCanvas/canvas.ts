import { max } from '../../../../utils/numbers'

export function renderPeakDataOnCanvas(
  canvas: HTMLCanvasElement,
  peakData: ReadonlyArray<number>,
  width: number,
  height: number,
): void {
  const ctx = canvas.getContext('2d')
  if (!ctx) {
    throw new Error(`Could not render peak data: can't get 2d context`)
  }

  const gradientBase = height * 0.75
  const rate = (peakData.length / width) * 3

  ctx.fillStyle = '#223344'
  ctx.globalCompositeOperation = 'xor'
  ctx.fillRect(0, 0, width, height)

  ctx.beginPath()
  for (let n = 0; n <= width; n += 1) {
    if (n % 3 === 2) {
      continue
    }

    const pos = Math.floor((peakData.length / width) * (n - (n % 3)))

    const leftRange = Math.max(0, pos - rate / 2)
    const rightRange = Math.min(peakData.length - 1, pos + rate / 2)
    const peak = (1 / 127) * max(peakData.slice(leftRange, rightRange))

    ctx.moveTo(n + 0.5, Math.floor(gradientBase - gradientBase * peak) - 1)
    ctx.lineTo(n + 0.5, Math.floor(gradientBase + (height - gradientBase) * peak) + 1)
  }
  ctx.strokeStyle = '#000000'
  ctx.stroke()
}
