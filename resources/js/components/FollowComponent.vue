<template>
  <div class="row">
    <div class="card" v-for="user in users" :key="user.id">
       <div class="col-md-12">
          <h3>{{ $user->first_name .' '. $user->last_name}}</h3>
          <a href="{{ route('user.follow', user) }}" class="btn btn-danger">Follow</a>
      </div>
    </div>
    <br/>
  </div>
</template>

<script>

export default {
    data() {
        return {
            users: [],
        }
    },
    methods: {
       loadNewUsers(){
         axios.get('/users').then((response => {
            this.users = response.data;
         }));
       }
    },
    mounted() {
      this.loadNewUsers();
      // update load when tweet added
      // Event.$on('added_tweet', () => {
      //     this.loadUserTweetsAndFollows();
      // });
  
    }
}
</script>