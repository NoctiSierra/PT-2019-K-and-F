<template>

    <div class="card" style="margin-bottom: 10px;">
        <div class="card-header">Les Commentaires</div>
        <div class="card-body" id="allCommentaires">
            <div v-for="comment in comments" :key="comment.id" style="margin-bottom: 20px;">
                <div class="card" style="margin-bottom: 10px;">
                    <div class="card-body">

                        <div v-if="(isEditable.value == true) && (isEditable.comment === comment)">
                            <textarea v-model="newContenu" style="width: 100%; padding: 10px;"></textarea>

                            <button v-on:click="editComment(comment)" class="btn btn-success">
                                Confirmer
                            </button>
                        </div>

                        <div v-else>
                            <p class="card-text">
                                {{ comment.contenu }}
                            </p>
                        </div>

                        <small class="text-muted">
                            Partagé le {{ comment.created_at }}
                        </small>

                        <small v-if="comment.updated_at != comment.created_at" class="text-muted">
                            - Modifié le {{ comment.updated_at }}
                        </small>

                    </div>
                </div>
                <div v-if="comment.user_id == iduser" style="text-align: right;">

                    <button v-on:click="changeStateIsEditable(comment)" class="btn btn-warning">
                        Editer
                    </button>

                    <button v-on:click="deleteComment(comment)" class="btn btn-danger">
                        Supprimer
                    </button>

                </div>
            </div>
            <div class="card">
                <div class="card-body">

                    <p class="card-text">
                        <textarea v-model="contenu" style="padding: 20px; border-radius: 20px; width: 100%; min-height: 200px;" required></textarea>
                    </p>

                    <p class="card-text" style="text-align: right;">
                        <button v-on:click="addComment()" class="btn btn-primary">Poster</button>
                        <button v-on:click="reset()" class="btn btn-danger">Effacer</button>
                    </p>

                </div>
            </div>
        </div>
    </div>

</template>
<script>
    export default {
        mounted() {
            // Récupérer tous les commentaires
            this.getComments();
        },

        // Variables provenant du composant parent
        props: ['idarticle', 'idredacteur', 'iduser'],

        data: function () {
            return {
                comments: [],
                comment: null,
                oldComment: null,
                contenu: '',
                newContenu: '',
                isEditable: {
                    comment: null,
                    value: false,
                },
            }
        },

        methods: {

            /**
             * Récupération de l'ensemble des commentaires
             */
            getComments: function () {
                axios.get('http://127.0.0.1:8000/articles/'+this.idarticle+'/comments')
                .then(response => {
                    this.comments = response.data.comments;
                })
                .catch(error => {
                    console.log(error);
                });
            },

            /**
             * Ajout d'un commentaire
             */
            addComment: function() {
                var comment = {
                    'contenu': this.contenu,
                };
                axios.put('http://127.0.0.1:8000/articles/'+this.idarticle, comment)
                .then(response => {
                    var index = this.comments.length;
                    //this.comments[index] = response.data;
                    this.comments.splice(index, 0, response.data);
                    this.contenu = '';
                });
            },

            changeStateIsEditable: function(comment) {

                this.isEditable.comment = comment;

                this.newContenu = comment.contenu;

                if(this.isEditable.value) {
                    var index = this.comments.indexOf(comment);
                    this.comments.splice(index, 1, comment);
                }

                this.isEditable.value = !this.isEditable.value;
            },

            /**
             * Modification d'un commentaire et mises à jour de l'affichage
             */
            editComment: function(comment) {
                this.isEditable.value = !this.isEditable;
                this.isEditable.comment = comment;

                // Modification du contenu
                var newComment = {
                    'contenu': this.newContenu, //comment.contenu,
                };

                // Modification du commentaire ciblé
                axios.post('http://127.0.0.1:8000/articles/'+this.idarticle+'/'+comment.id, newComment)
                .then(response => {
                    var commentRes = response.data;
                    var index = this.comments.indexOf(comment);
                    this.comments.splice(index, 1, commentRes);
                })
                .catch(error => {
                    console.log(error);
                });
            },

            /**
             * Suppression d'un commentaire et mise à jour de l'affichage
             */
            deleteComment: function(comment) {
                axios.delete('http://127.0.0.1:8000/articles/'+this.idarticle+'/'+comment.id)
                .then(response => {
                    var index = this.comments.indexOf(comment);
                    this.comments.splice(index, 1);
                })
                .catch(error => {
                    console.log(error);
                });
            },
        }
    }

</script>

