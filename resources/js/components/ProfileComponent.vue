<template>
  <div class="row">
    <div class="col-md-4">{{ user.tweetCount }}</div>
    <div class="col-md-4">{{ user.followingCount }}</div>
    <div class="col-md-4">{{ user.followersCount }}</div>
  </div>
</template>

<script>

export default {
    data() {
        return {
            user: [],
        }
    },
    methods: {
       loadUserTweetsAndFollows(){
         axios.get('/profile-info').then((response => {
            this.user = response.data;
         }));
       }
    },
    mounted() {
      this.loadUserTweetsAndFollows();
    },
    created() {
        // on add tweet event, update total
        Event.$on('add-tweet', obj => {
            this.loadUserTweetsAndFollows();
        });
        // on delete tweet event, update total
        Event.$on('delete-tweet', obj => {
            this.loadUserTweetsAndFollows();
        });
    }
}
</script>