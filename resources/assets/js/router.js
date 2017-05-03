import Vue from 'vue'
import Router from 'vue-router'

import isMobile from 'ismobilejs'
import { each } from 'lodash'

import { loadMainView } from './utils'
import { artistStore, songStore, queueStore, playlistStore, userStore, genreStore } from './stores'
import { playback } from './services'

import MinimalLayout from './components/templates/minimal-layout'
import MainLayout from './components/templates/main-layout'

import Login from './components/contents/login'

Vue.use(Router)

export default new Router({
    linkActiveClass: '',
    routes: [
        {
            path: '/auth',
            component: MinimalLayout,
            children: [
                {
                    path: 'login',
                    component: Login
                }
            ]
        },
        {
            path: '/',
            component: MainLayout,
            beforeEnter: (to, from, next) => {
                // Check here if user is authenticated
                if(!userStore.getters.isAuthenticated())
                    next('auth/login')
            },
            children: [
                {
                    path: '/',
                    component: Login
                }/*,
                {
                    path: '/individual',
                    component: Individual
                },
                {
                    path: '/professional',
                    component: Professional
                },
                {
                    path: '/preferences',
                    component: Preferences
                }*/
            ]
        }
    ]
})

/*export default {
  routes: {
    '/home' () {
      loadMainView('home')
    },

    '/queue' () {
      loadMainView('queue')
    },

    '/songs' () {
      loadMainView('songs')
    },

    '/albums' () {
      loadMainView('albums')
    },

    '/album/(\\d+)' (id) {
      loadMainView('album', id)
    },

    '/artists' () {
      loadMainView('artists')
    },

    '/artist/(\\d+)' (id) {
      loadMainView('artist', id)
    },

    '/genres' () {
      loadMainView('genres')
    },

    '/genre/(\\d+)' (id) {
      loadMainView('genre', id)
    },

    '/favorites' () {
      loadMainView('favorites')
    },

    '/playlist/(\\d+)' (id) {
      const playlist = playlistStore.byId(~~id)
      if (playlist) {
        loadMainView('playlist', playlist)
      }
    },

    '/settings' () {
      userStore.current.is_admin && loadMainView('settings')
    },

    '/users' () {
      userStore.current.is_admin && loadMainView('users')
    },

    '/profile' () {
      loadMainView('profile')
    },

    '/login' () {

    },

    '/song/([a-z0-9]{32})' (id) {
      const song = songStore.byId(id)
      if (!song) return

      if (isMobile.apple.device) {
        // Mobile Safari doesn't allow autoplay, so we just queue.
        queueStore.queue(song)
        loadMainView('queue')
      } else {
        playback.queueAndPlay(song)
      }
    },

    '/youtube' () {
      loadMainView('youtubePlayer')
    }
  },

  init () {
    this.loadState()
    window.addEventListener('popstate', () => this.loadState(), true)
  },

  loadState () {
    if (!window.location.hash) {
      return this.go('home')
    }

    each(Object.keys(this.routes), route => {
      const matches = window.location.hash.match(new RegExp(`^#!${route}$`))
      if (matches) {
        const [, ...params] = matches
        this.routes[route](...params)
        return false
      }
    })
  },


  go (path) {
    if (path[0] !== '/') {
      path = `/${path}`
    }

    if (path.indexOf('/#!') !== 0) {
      path = `/#!${path}`
    }

    document.location.href = `${document.location.origin}${path}`
  }
}
*/