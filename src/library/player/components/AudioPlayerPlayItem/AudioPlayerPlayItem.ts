import { IComponentController, IComponentOptions } from 'angular'
import templateUrl from './AudioPlayerPlayItem.html'
import classes from './AudioPlayerPlayItem.module.less'
import { AudioPlayerQueueService } from '../../services/AudioPlayerQueueService'
import { trackToQueueEntry } from './utils'

const COMPONENT_NAME = 'audioPlayerPlayItem'

class AudioPlayerPlayItemController implements IComponentController {
  public track!: Track
  public trackList!: ReadonlyArray<Track>

  public classes = classes

  static $inject = [AudioPlayerQueueService.name]

  constructor(private readonly audioPlayerQueueService: AudioPlayerQueueService) {}

  public async play(): Promise<void> {
    const itemIndex = this.trackList.findIndex(({ id }) => id === this.track.id)
    if (itemIndex === -1) return
    const queue = this.trackList.map(trackToQueueEntry)
    await this.audioPlayerQueueService.play(queue, itemIndex)
  }
}

export const AudioPlayerPlayItem: Record<typeof COMPONENT_NAME, IComponentOptions> = {
  [COMPONENT_NAME]: {
    bindings: {
      track: '=',
      trackList: '=',
    },
    transclude: true,
    templateUrl,
    controller: AudioPlayerPlayItemController,
  },
}
