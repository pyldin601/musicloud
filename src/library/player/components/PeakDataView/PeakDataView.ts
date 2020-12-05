import { IComponentOptions, IAugmentedJQuery } from 'angular'
import makeDebug from 'debug'
import classes from './PeakDataView.module.less'
import { renderPeakDataOnCanvas } from './canvas'
import { PeakData } from '../../services/PeakDataService'

const COMPONENT_NAME = 'peakDataView'

class PeakDataViewController {
  private debug = makeDebug(PeakDataViewController.name)

  public peakData?: PeakData

  static $inject = ['$element', '$window']

  constructor(private readonly $element: IAugmentedJQuery, private readonly $window: Window) {}

  public $postLink() {
    this.debug('$postLink')
    this.resizeCanvas()
    this.$window.addEventListener('resize', this.resizeCanvas)
  }

  public $onChanges() {
    this.debug('$onChanges')
    this.renderPeakData()
  }

  public $destroy() {
    this.debug('$destroy')
    this.$window.removeEventListener('resize', this.resizeCanvas)
  }

  private resizeCanvas = (): void => {
    this.debug('resizeCanvas')

    const canvas = this.$element.find('canvas')[0]
    if (!canvas) {
      throw new Error(`Could not resize canvas: canvas element not found`)
    }

    canvas.width = canvas.clientWidth
    canvas.height = canvas.clientHeight

    this.renderPeakData()
  }

  private renderPeakData(): void {
    this.debug('renderPeakData')

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

    renderPeakDataOnCanvas(canvas, this.peakData ?? [], canvas.width, canvas.height)
  }
}

export const PeakDataView: Record<typeof COMPONENT_NAME, IComponentOptions> = {
  [COMPONENT_NAME]: {
    template: `<canvas class="${classes.root}" ng-show="$ctrl.peakData"></canvas>`,
    controller: PeakDataViewController,
    bindings: {
      peakData: '<',
    },
  },
}
