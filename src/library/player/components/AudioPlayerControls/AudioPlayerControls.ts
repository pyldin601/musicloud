import templateUrl from './AudioPlayerControls.html'

const COMPONENT_NAME = 'audioPlayerControls'

class AudioPlayerControlsController {
  public get isPlaying(): boolean {
    return false
  }

  public get isBuffering(): boolean {
    return false
  }

  public playPrevious(): void {
    //
  }

  public playNext(): void {
    //
  }

  public playOrPause(): void {
    //
  }
}

export const AudioPlayerControls = {
  [COMPONENT_NAME]: {
    templateUrl,
    controller: AudioPlayerControlsController,
  },
}
