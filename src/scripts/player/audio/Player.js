/*
 * Copyright (c) 2017 Roman Lakhtadyr
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

import $ from 'jquery';
import EventEmitter from 'events';

export default class Player extends EventEmitter {
  jFrame;

  constructor(pathToSwf) {
    super();

    this.jFrame = $('div');
    this.initPlayer(pathToSwf);
    this.initBindings();
  }

  initPlayer(pathToSwf) {
    this.jFrame.jPlayer({
      swfPath: pathToSwf,
      supplied: 'mp3',
      solution: 'html',
    });
  }

  initBindings() {
    const emitter = this;

    this.jFrame.bind($.jPlayer.event.ready, event =>
      emitter.emit('ready', event),
    );
    this.jFrame.bind($.jPlayer.event.play, event =>
      emitter.emit('play', event),
    );
    this.jFrame.bind($.jPlayer.event.pause, event =>
      emitter.emit('pause', event),
    );
    this.jFrame.bind($.jPlayer.event.waiting, event =>
      emitter.emit('waiting', event),
    );
    this.jFrame.bind($.jPlayer.event.playing, event =>
      emitter.emit('playing', event),
    );
    this.jFrame.bind($.jPlayer.event.canplay, event =>
      emitter.emit('canplay', event),
    );
    this.jFrame.bind($.jPlayer.event.seeking, event =>
      emitter.emit('seeking', event),
    );
    this.jFrame.bind($.jPlayer.event.seeked, event =>
      emitter.emit('seeked', event),
    );
    this.jFrame.bind($.jPlayer.event.ended, event =>
      emitter.emit('ended', event),
    );
    this.jFrame.bind($.jPlayer.event.volumechange, event =>
      emitter.emit('volumechange', event),
    );
    this.jFrame.bind($.jPlayer.event.timeupdate, event =>
      emitter.emit('timeupdate', event.jPlayer.status.currentTime, event),
    );
  }

  async load(url) {
    this.jFrame.jPlayer('setMedia', { mp3: url });
  }

  async play(position) {
    this.jFrame.jPlayer('play', position);
  }

  async pause() {
    this.jFrame.jPlayer('pause');
  }

  async stop() {
    this.jFrame.jPlayer('stop');
  }

  async close() {
    this.jFrame.jPlayer('clearMedia');
  }

  async seek(position) {
    this.jFrame.jPlayer('playHead', position);
  }

  async volume(volume) {
    this.jFrame.jPlayer('volume', volume);
  }
}
