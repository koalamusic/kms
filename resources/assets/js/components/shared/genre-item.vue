<template>
  <article class="item" v-if="genre.songCount" draggable="true" @dragstart="dragStart">
    <footer>
      <div class="info">
        <a class="name" :href="'/#!/genre/'+genre.id">{{ genre.name }}</a>
      </div>
      <p class="meta">
        <span class="left">
          <i class="nowrap">{{ genre.songCount | pluralize('song') }}</i>
        </span>
        <span class="right">
          <a class="control" @click.prevent="play">
            <i class="fa fa-play"></i>
          </a>
          <a href @click.prevent="shuffle" title="Shuffle">
            <i class="fa fa-random"></i>
          </a>
        </span>
      </p>
    </footer>
  </article>
</template>

<script>
import { map } from 'lodash'

import { pluralize } from '../../utils'
import { queueStore, sharedStore, genreStore } from '../../stores'
import { playback } from '../../services'

export default {
  name: 'shared--genre-item',
  props: ['genre'],
  filters: { pluralize },

  data () {
    return {
      sharedState: sharedStore.state
    }
  },

  computed: { },

  methods: {
    /**
     * Play all songs in the current genre in track order,
     * or queue them up if Ctrl/Cmd key is pressed.
     */
    play (e) {
      genreStore.getSongs(this.genre).then(function(songs) {
        if (e.metaKey || e.ctrlKey) {
          queueStore.queue(songs)
        } else {
          playback.queueAndPlay(songs, false)
        }
      }).catch(function() {
        console.log('Songs loading error')
      })
    },

    /**
     * Shuffle all songs in genre.
     */
    shuffle () {
      playback.playAllInGenre(this.genre, true)
    },

    /**
     * Allow dragging the genre (actually, its songs).
     */
    dragStart (e) {
      const songIds = map(this.genre.songs, 'id')
      e.dataTransfer.setData('application/x-koel.text+plain', songIds)
      e.dataTransfer.effectAllowed = 'move'

      // Set a fancy drop image using our ghost element.
      const ghost = document.getElementById('dragGhost')
      ghost.innerText = `All ${pluralize(songIds.length, 'song')} in ${this.genre.name}`
      e.dataTransfer.setDragImage(ghost, 0, 0)
    }
  }
}
</script>

<style lang="scss">
@import "../../../sass/partials/_vars.scss";
@import "../../../sass/partials/_mixins.scss";

@include artist-album-card();

.sep {
  display: none;
  color: $color2ndText;

  .as-list & {
    display: inline;
  }
}
</style>
