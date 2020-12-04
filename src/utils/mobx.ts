import PQueue from 'p-queue'
import { IReactionDisposer, IReactionOptions, IReactionPublic, reaction } from 'mobx'
import makeDebug from 'debug'

export function queuedReaction<T>(
  expression: (r: IReactionPublic) => T,
  effect: (arg: T, prev: T, r: IReactionPublic) => Promise<void>,
  opts?: IReactionOptions,
): IReactionDisposer {
  const debug = makeDebug('queuedReaction')
  const queue = new PQueue({ concurrency: 1 })

  let size = 0

  return reaction(
    expression,
    async (arg: T, prev: T, r: IReactionPublic) => {
      if (size > 0) {
        debug('queuedReaction: Queue has pending reactions', { size })
      }
      size += 1
      try {
        await queue.add(() => effect(arg, prev, r))
      } finally {
        size -= 1
      }
    },
    opts,
  )
}
