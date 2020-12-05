import { IComponentOptions, IAugmentedJQuery } from 'angular'
import './PeakDataCanvas.less'
import { renderPeakDataOnCanvas } from './canvas'

const COMPONENT_NAME = 'peakDataCanvas'

class PeakDataCanvasController {
  private peakData?: ReadonlyArray<number>

  static $inject = ['$element', '$window']

  constructor(private $element: IAugmentedJQuery, private $window: Window) {}

  public $postLink() {
    this.resizeCanvas()
    this.$window.addEventListener('resize', this.resizeCanvas)
  }

  public $destroy() {
    this.$window.removeEventListener('resize', this.resizeCanvas)
  }

  public $onChanges() {
    this.renderPeakData()
  }

  private resizeCanvas = (): void => {
    const canvas = this.$element.find('canvas')[0]
    if (!canvas) {
      throw new Error(`Could not render peak data: canvas element not found`)
    }

    canvas.width = canvas.clientWidth
    canvas.height = canvas.clientHeight

    this.renderPeakData()
  }

  private renderPeakData(): void {
    const element = this.$element[0]
    if (!element) {
      throw new Error(`Could not render peak data: element not found`)
    }

    const canvas = this.$element.find('canvas')[0]
    if (!canvas) {
      throw new Error(`Could not render peak data: canvas element not found`)
    }

    const ctx = canvas.getContext('2d')
    if (!ctx) {
      throw new Error(`Could not render peak data: can't get 2d context`)
    }

    const peakData = this.peakData
    if (!peakData) {
      ctx.clearRect(0, 0, canvas.width, canvas.height)
      return
    }

    renderPeakDataOnCanvas(canvas, peakData, canvas.width, canvas.height)
  }
}

export const PeakDataCanvas: Record<typeof COMPONENT_NAME, IComponentOptions> = {
  [COMPONENT_NAME]: {
    template: `
      <canvas class="waveform-canvas"></canvas>
    `,
    controller: PeakDataCanvasController,
    bindings: {
      peakData: '<',
    },
  },
}
