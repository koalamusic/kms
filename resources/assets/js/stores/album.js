/*eslint camelcase: ["error", {properties: "never"}]*/

import Vue from 'vue'
import Vuex from 'vuex'
import { reduce, each, union, difference, take, filter, orderBy, assign } from 'lodash'
import { http, playback } from '../services'
import { secondsToHis } from '../utils'
import stub from '../stubs/album'
import axios from 'axios'
import { songStore, artistStore, queueStore } from '.'

Vue.use(Vuex)

export const albumStore = new Vuex.Store({
    state: {
        albums: [],
        album: stub,
        songs: [],
        stub: stub
    },
    actions: {
        LOAD_ALBUMS ({ commit }) {
            axios.get('albums').then(({ data }) => {
                commit('SET_ALBUMS', { albums: data.albums })
            }, error => {
                console.log("Albums loading error")
            })
        },
        LOAD_ALBUM ({ commit }, id) {
            axios.get('albums/' + id).then(({ data }) => {
                commit('SET_ALBUM', { album: data.album })
            }, error => {
                console.log("Album " + id + " loading error")
            })
        },
        LOAD_ALBUM_SONGS ({ commit }, id) {
            axios.get('albums/' + id + '/songs').then(({ data }) => {
                commit('SET_ALBUM_SONGS', { songs: data.songs })
            }, error => {
                console.log("Loading songs for album " + id + " error")
            })
        },
        ADD_TO_QUEUE ({ commit }, [id, play, shuffle]) {
            axios.get('albums/' + id + '/songs').then(({ data }) => {
                let songs = songStore.setupSongs(data.songs)
                if (play) {
                    playback.queueAndPlay(songs, shuffle)
                } else {
                    queueStore.queue(songs)
                }
            }, error => {
                console.log("Loading songs for album " + id + " error")
            })
        }
    },
    mutations: {
        SET_ALBUMS: (state, { albums }) => {
            state.albums = albums
        },
        SET_ALBUM: (state, { album }) => {
            state.album = album
        },
        SET_ALBUM_SONGS: (state, { songs }) => {
            state.songs = songs
        }
    },
    getters: {
    },
    modules: {

    },

    setupAlbum (album, artist) {
        Vue.set(album, 'info', null)
        this.getLength(album)

        return album
    },

    /**
     * Get the total length of an album by summing up its songs' duration.
     * The length will also be converted into a H:i:s format and stored as fmtLength.
     *
     * @param  {Object} album
     *
     * @return {String} The H:i:s format of the album length.
     */
    getLength (album) {
        Vue.set(album, 'length', reduce(album.songs, (length, song) => length + song.length, 0))
        Vue.set(album, 'fmtLength', secondsToHis(album.length))

        return album.fmtLength
    },

    /**
     * Add new album/albums into the current collection.
     *
     * @param  {Array.<Object>|Object} albums
     */
    add (albums) {
        albums = [].concat(albums)
        each(albums, album => {
            this.setupAlbum(album, album.artist)
            album.playCount = reduce(album.songs, (count, song) => count + song.playCount, 0)
        })

        this.all = union(this.all, albums)
    },

    /**
     * Add song(s) into an album.
     *
     * @param {Object} album
     * @param {Array.<Object>|Object} song
     */
    addSongsIntoAlbum (album, songs) {
        songs = [].concat(songs)

        album.songs = union(album.songs || [], songs)

        each(songs, song => {
            song.albumId = album.id
            song.album = album
        })

        album.playCount = reduce(album.songs, (count, song) => count + song.playCount, 0)
        this.getLength(album)
    },

    /**
     * Remove song(s) from an album.
     *
     * @param  {Object} album
     * @param  {Array.<Object>|Object} songs
     */
    removeSongsFromAlbum (album, songs) {
        album.songs = difference(album.songs, [].concat(songs))
        album.playCount = reduce(album.songs, (count, song) => count + song.playCount, 0)
        this.getLength(album)
    },

    /**
     * Checks if an album is empty.
     *
     * @param  {Object}  album
     *
     * @return {boolean}
     */
    isAlbumEmpty (album) {
        return !album.songs.length
    },

    /**
     * Remove album(s) from the store.
     *
     * @param  {Array.<Object>|Object} albums
     */
    remove (albums) {
        albums = [].concat(albums)
        this.all = difference(this.all, albums)

        // Remove from the artist as well
        each(albums, album => {
            artistStore.removeAlbumsFromArtist(album.artist, album)

            // Delete the cache while we're here
            delete this.cache[album.id]
        })
    },

    /**
     * Get top n most-played albums.
     *
     * @param  {Number} n
     *
     * @return {Array.<Object>}
     */
    getMostPlayed (n = 6) {
        // Only non-unknown albums with actually play count are applicable.
        const applicable = filter(this.all, album => album.playCount && album.id !== 1)
        return take(orderBy(applicable, 'playCount', 'desc'), n)
    },

    /**
     * Get n most recently added albums.
     *
     * @param  {Number} n
     *
     * @return {Array.<Object>}
     */
    getRecentlyAdded (n = 6) {
        const applicable = filter(this.all, album => album.id !== 1)
        return take(orderBy(applicable, 'created_at', 'desc'), n)
    }
})