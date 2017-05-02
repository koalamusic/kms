<template>
    <div id="app">
        <div id="main" tabindex="0" v-show="authenticated"
             @keydown.space="togglePlayback"
             @keydown.j = "playNext"
             @keydown.k = "playPrev"
             @keydown.f = "search"
             @keydown.mediaPrev = "playPrev"
             @keydown.mediaNext = "playNext"
             @keydown.mediaToggle = "togglePlayback"
        >
            <site-header/>
            <router-view></router-view>
            <site-footer/>
            <overlay ref="overlay"/>
            <edit-songs-form ref="editSongsForm"/>
        </div>
    </div>
</template>


<script>
    export default {
        components: {},
        name: 'main-layout'
    }
</script>