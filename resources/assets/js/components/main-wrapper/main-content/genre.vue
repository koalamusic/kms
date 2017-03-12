<template>
  <section id="genreWrapper">
    <h1 class="heading">
      <span class="overview">
        {{ genre.name }}
        <controls-toggler :showing-controls="showingControls" @toggleControls="toggleControls"/>

        <span class="meta" v-show="meta.songCount">
          <i class="nowrap">{{ meta.songCount | pluralize('song') }}</i>
          •
          <i class="nowrap">{{ meta.totalLength }}</i>
        </span>
      </span>

      <song-list-controls
        v-show="genre.songCount && (!isPhone || showingControls)"
        @shuffleAll="shuffleAll"
        @shuffleSelected="shuffleSelected"
        :config="songListControlConfig"
        :selectedSongs="selectedSongs"
      />
    </h1>

    <song-list :items="songs" type="genre"/>

  </section>
</template>

<script>
import isMobile from 'ismobilejs'

import { pluralize, event } from '../../../utils'
import { genreStore, sharedStore } from '../../../stores'
import { playback } from '../../../services'
import router from '../../../router'
import hasSongList from '../../../mixins/has-song-list'

export default {
  name: 'main-wrapper--main-content--genre',
  mixins: [hasSongList],
  components: { },
  filters: { pluralize },

  data () {
    return {
      sharedState: sharedStore.state,
      genre: genreStore.stub,
      isPhone: isMobile.phone,
      showingControls: false,
      songs: []
    }
  },

  computed: {

  },

  watch: {
    /**
     * Watch the genre's song count.
     * If this is changed to 0, the user has edit the songs in this genre
     * and move all of them into another genre.
     * We should then go back to the genre list.
     */
    'songs.length' (newVal) {
      if (!newVal) {
        router.go('genres')
      }
    }
  },

  created () {
    /**
     * Listen to 'main-content-view:load' event to load the requested genre
     * into view if applicable.
     *
     * @param {String} view   The view name
     * @param {Object} genre  The genre object
     */
    event.on('main-content-view:load', (view, id) => {
      if (view === 'genre') {
        this.init(id)
      }
    })
  },

  methods: {
    init(id) {
      if(id != 0) {
        var self = this
        genreStore.byId(id).then(function(genre) {
          self.genre = genre

          self.loadSongs()
        }).catch(function() {
          console.log("Genre loading error")
        })
      }
    },
    loadSongs() {
      var self = this
      genreStore.getSongs(this.genre).then(function (songs) {
        self.songs = songs
      }).catch(function () {
        console.log('Songs loading error')
      })
    },
    /**
     * Shuffle the songs in the current genre.
     */
    shuffle () {
      playback.queueAndPlay(this.genre.songs, true)
    },
    /**
     * Overload the mixin default shuffleAll method, since we don't have this.state
     */
    shuffleAll () {
      this.shuffle()
    }
  }
}
</script>

<style lang="scss" scoped>
@import "../../../../sass/partials/_vars.scss";
@import "../../../../sass/partials/_mixins.scss";

#genreWrapper {
  button.play-shuffle {
    i {
      margin-right: 0 !important;
    }
  }

  .heading {
    .overview {
      position: relative;
      padding-left: 84px;

      @media only screen and (max-width : 768px) {
        padding-left: 0;
      }
    }

    .cover {
      position: absolute;
      left: 0;
      top: -7px;

      @media only screen and (max-width : 768px) {
        display: none;
      }
    }
  }
}
</style>
