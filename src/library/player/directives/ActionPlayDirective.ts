import { IDirectiveFactory, Injectable, IScope } from 'angular'
import { AudioPlayerQueueService } from '../services/AudioPlayerQueueService'
import { Track } from '../../../scripts/musicloud/services/TrackService'

// todo refactor this

interface Scope extends IScope {
  actionPlay: Track
  actionContext: ReadonlyArray<Track>
}

export class ActionPlayDirectiveController {
  static $inject = [AudioPlayerQueueService.name]
  constructor(readonly audioPayerQueueService: AudioPlayerQueueService) {}
}

export const ActionPlayDirective: Record<
  string,
  Injectable<IDirectiveFactory<Scope, JQLite, never, ActionPlayDirectiveController>>
> = {
  actionPlay: () => ({
    scope: {
      actionPlay: '=',
      actionContext: '=',
      // actionResolver: '=', // todo?
    },
    restrict: 'A',
    controller: ActionPlayDirectiveController,
    link: (scope, elem, _, controller): void => {
      elem.on('dblclick', async () => {
        const track = scope.actionPlay
        const tracks = scope.actionContext

        const index = tracks.findIndex((t) => t.id === track.id)

        if (index !== -1) {
          const queueEntries = tracks.map((track) => ({
            trackId: track.id,
            title: track.track_title,
            artist: track.track_artist,
            length: track.length,
            src: track.format === 'mp3' ? `/file/${track.file_id}` : `/preview/${track.id}`,
            rating: track.track_rating,
          }))
          await controller?.audioPayerQueueService.play(queueEntries, index)
        }
      })
    },
  }),
}
