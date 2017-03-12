<template>
  <section id="songsWrapper">
    <h1 class="heading">
      <span>All Songs
        <controls-toggler :showing-controls="showingControls" @toggleControls="toggleControls"/>

        <span class="meta" v-show="meta.songCount">
          <i class="nowrap">{{ meta.songCount | pluralize('song') }}</i>
          •
          <i class="nowrap">{{ meta.totalLength }}</i>
        </span>
      </span>

      <song-list-controls
        v-show="datas.length && (!isPhone || showingControls)"
        @shuffleAll="shuffleAll"
        @shuffleSelected="shuffleSelected"
        :config="songListControlConfig"
        :selectedSongs="selectedSongs"
      />
    </h1>

    <song-list :items="datas" type="allSongs" ref="songList"/>
  </section>
</template>

<script>
import { pluralize, event } from '../../../utils'
import { songStore } from '../../../stores'
import hasSongList from '../../../mixins/has-song-list'

export default {
  name: 'main-wrapper--main-content--songs',
  mixins: [hasSongList],
  filters: { pluralize },

  data () {
    return {
      datas: []
    }
  },
  methods: {
    init() {
      var self = this
      songStore.init().then(function(songs) {
        self.datas = songs
      }).catch(function() {
        console.log("Songs loading error")
      })
    }
  },
  created () {
    this.init()
    /**
     * Listen to 'main-content-view:load' event to load the requested album
     * into view if applicable.
     *
     * @param {String} view   The view name
     * @param {Object} album  The album object
     */
    event.on('main-content-view:load', (view, album) => {
      /*if (view === 'album') {
        this.info.showing = false
        this.album = album
        */// #530
        this.$nextTick(() => {
          // this.$refs.songList.sort()
        })
      //}
    })
  },
}
</script>

<style lang="scss">
@import "../../../../sass/partials/_vars.scss";
@import "../../../../sass/partials/_mixins.scss";
</style>

