<template>
  <section id="mainContent">
    <div class="translucent" :style="{ backgroundImage: albumCover ? 'url('+albumCover+')' : 'none' }"></div>
    <home v-show="view === 'home'"/>
    <queue v-show="view === 'queue'"/>
    <songs v-show="view === 'songs'"/>
    <albums v-show="view === 'albums'"/>
    <album v-show="view === 'album'"/>
    <artists v-show="view === 'artists'"/>
    <artist v-show="view === 'artist'"/>
    <genres v-show="view === 'genres'"/>
    <genre v-show="view === 'genre'"/>
    <users v-show="view === 'users'"/>
    <settings v-show="view === 'settings'"/>
    <playlist v-show="view === 'playlist'"/>
    <favorites v-show="view === 'favorites'"/>
    <profile v-show="view === 'profile'"/>
    <youtube-player v-if="sharedState.useYouTube" v-show="view === 'youtubePlayer'"/>
  </section>
</template>

<script>
import { event } from '../../../utils'
import { albumStore, sharedStore } from '../../../stores'

import albums from './albums.vue'
import album from './album.vue'
import genres from './genres.vue'
import genre from './genre.vue'
import artists from './artists.vue'
import artist from './artist.vue'
import songs from './songs.vue'
import settings from './settings.vue'
import users from './users.vue'
import queue from './queue.vue'
import home from './home.vue'
import playlist from './playlist.vue'
import favorites from './favorites.vue'
import profile from './profile.vue'
import youtubePlayer from './youtube-player.vue'

export default {
  components: { albums, album, artists, artist, genres, genre, songs, settings,
    users, home, queue, playlist, favorites, profile, youtubePlayer },

  data () {
    return {
      view: 'home', // The default view
      albumCover: null,
      sharedState: sharedStore.state
    }
  },

  created () {
    event.on({
      'main-content-view:load': view => {
        this.view = view
      },

      /**
       * When a new song is played, find its cover for the translucent effect.
       *
       * @param  {Object} song
       *
       * @return {Boolean}
       */
      'song:played': song => {
        this.albumCover = song.album.cover === albumStore.stub.cover ? null : song.album.cover
      }
    })
  }
}
</script>
