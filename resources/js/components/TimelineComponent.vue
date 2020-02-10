<template>
    <div class="container">
            <div id="app-instasearch">
                 <form method="get" @submit.prevent="getSearchedHash">
                      <div class="input-container">
                        <input class="form-control" type="text" v-model="hashSearch" placeholder="Search Tweet or Hashtag without #">
                        <button type="submit" class="btn btn-light">Search</button>
                      </div>
                </form>
            </div>
            <div class="col-lg-12 d-flex justify-content-end pb-2">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#create-item">
                    Add New Tweet <i class="fa fa-plus" aria-hidden="true"></i>
                </button>
            </div>


            <div class="row timeLineRow">
                <div class="col-md-12 offset-md-12">
                    <h4>Latest Tweets</h4>
                    <ul class="timeline">
                        <li v-for="tweet in items" :key="tweet.id">
                            <p v-if="tweet.user">
                                 <a>{{ tweet.user.first_name }} {{ tweet.user.last_name }}</a>
                            </p>
                            <p v-else>No name</p>
                            <a href="#" class="float-right">{{ tweet.createdDate }}</a>
                            <p v-if="tweet.photo">
                                <img class="tweetImage" v-bind:src="tweet.photo" />
                            </p>
                            <p>{{ tweet.text }}</p>
                            <p v-if="tweet.hash_tag">
                                <a href="#"><i>#{{ tweet.hash_tag }}</i></a>
                            </p>
                            <span v-if="tweet.user" >
                                <div v-if="tweet.liked">
                                   <button class="btn btn-link" @click="unlikeTweet(tweet.id)">Unlike</button>
                                </div>
                                <div v-else>
                                    <button class="btn btn-link" @click="likeTweet(tweet.id)">Like</button>
                                </div>
                            </span>
                            <span v-if="tweet.user" class="actionBtn">
                                <div v-if="userId == tweet.user.id">
                                    <button type="button" class="btn btn-primary" @click.prevent="editItem( tweet )" >Edit<i class="fa fa-pencil" aria-hidden="true"></i></button>
                                    <button  type="button" class="btn btn-danger" @click.prevent="deleteItem( tweet )">Delete<i class="fa fa-trash" aria-hidden="true"></i></button>
                                </div>
                            </span>
                            <br/> <br/>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Create Item Modal -->
            <div class="modal fade" id="create-item" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">New Tweet</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form method="post" enctype="multipart/form-data" @submit.prevent="createItem">
                            <div class="modal-body">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="title">Content</label>
                                            <textarea class="form-control" id="text" placeholder="Enter title" v-model="newItem.text"></textarea>
                                        </div>
                                    </div>

                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="title">Photo</label>
                                             <input type="file" id="photo" v-on:change="onFileChange" class="form-control"/>
                                        </div>
                                    </div>

                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="description">Hash Tag</label>
                                            <input type="text" class="form-control" id="hash_tag" rows="3" placeholder="#Enter hash tag" v-model="newItem.hash_tag">
                                        </div>
                                    </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Eit item Modal -->
            <div class="modal fade" id="edit-item" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Update Tweet</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="form-group">
                            <p v-if="fillItem.photo">
                            <label>Select new image to change this.</label>
                                <img class="tweetImage" v-bind:src="fillItem.photo" />
                            </p>
                        </div>
                        <form method="post" enctype="multipart/form-data" @submit.prevent="updateItem( fillItem.id )">
                            <input type="hidden" name="_method" value="PUT">
                            <div class="modal-body">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="title">Content</label>
                                            <textarea class="form-control" id="text" placeholder="Enter text" v-model="fillItem.text"></textarea>
                                        </div>
                                    </div>

                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="title">Select New Photo</label>
                                             <input type="file" id="photo" v-on:change="onFileChange" class="form-control"/>
                                        </div>
                                    </div>

                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="description">Hash Tag</label>
                                            <input type="text" class="form-control" id="hash_tag" rows="3" placeholder="#Enter hash tag" v-model="fillItem.hash_tag"></textarea>
                                        </div>
                                    </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        <!-- </div> -->
    </div>
</template>

<script>
    export default {
        mounted() {
            this.userId = document.querySelector("meta[name='user-id']").getAttribute('content');
            this.getItems();
        },
        data: () => ({
            items: [],
            image:'',
            newItem: { 
                'text': '', 
                'hash_tag': '', 
                'photo': '', 
            },
            fillItem: { 'text': '', 'hash_tag': '', 'photo': '', 'id': ''},
            userId : '',
            liked: false,
            hashSearch: ''
        }),
        methods: {
            onFileChange(e) {
                let files = e.target.files || e.dataTransfer.files;
                if (!files.length)
                    return;
                this.createImage(files[0]);
            },
            getSearchedHash() {
                this.getItems();
            },
            createImage(file) {
                let reader = new FileReader();
                let vm = this;
                reader.onload = (e) => {
                    vm.image = e.target.result;
                };
                reader.readAsDataURL(file);
            },
            likeTweet(tweetId) {
                axios.post('tweet/like/' + tweetId ).then( (response) => {
                    this.getItems();
                }).catch( (error) => {
                    console.log( error.response.data )
                })
            },
            unlikeTweet(tweetId) {
                axios.post('tweet/unlike/' + tweetId ).then( (response) => {
                    this.getItems();
                }).catch( (error) => {
                    console.log( error.response.data )
                })
            },
            getItems() {
                let hashSearch = (this.hashSearch) ? '?hashSearch=' + this.hashSearch : '';
                axios.get('tweet-items' + hashSearch).then( response => {
                    let answer = response.data;
                    this.items = answer.items;
                })
            },
            createItem() {
                let input = this.newItem;
                input.photo = this.image;

                axios.post('tweet-items', input).then( (response) => {
                    this.getItems();
                    this.items.push(response.data);

                    // make emit event
                    Event.$emit('add-tweet', this.items);

                    this.newItem = { 
                        'text': '', 
                        'hash_tag': '', 
                        'photo': '', 
                    };
                    $('#create-item').modal('hide');
                }).catch( (error) => {
                    console.log( error.response.data )
                })
            },
            editItem( item ) {
                let edit = this.fillItem;
                edit.text = item.text;
                edit.photo = item.photo;
                edit.hash_tag = item.hash_tag;
                edit.id = item.id;
                $("#edit-item").modal('show');
            },
            updateItem( id ){
                let input = this.fillItem;
                input.photo = this.image;
                axios.put('tweet-items/' + id, input).then( (response)=> {
                    this.getItems();
                    this.fillItem = {'text': '', 'hash_tag': '', 'photo': '', 'id': ''};
                    alert('Tweet updated sucessfully!!');
                    $('#edit-item').modal('hide');
                }).catch( (error)=> {
                    console.log( error.response.data )
                })
            },
            deleteItem( item ) {
                if(confirm("Do you really want to delete?")){
                    axios.delete('tweet-items/' + item.id).then( (response) => {
                        this.getItems();
                        Event.$emit('delete-tweet', this.items);
                    }).catch(error => {
                      console.log(error);
                    });
                }
            },
        }
    }
</script>