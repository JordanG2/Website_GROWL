<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>page profil Perso</title>
    <link rel="stylesheet" href="css/profil.css">
    
</head>

<body>
    <?php 
        require_once("./php/constant.php");
        include_once(HEADER);
        if(isset($_GET["user"])  && db_alreadyExist('user', 'Username', "'".$_GET["user"]."'")) {
            $vieweduser_id = db_selectColumns('user',['UserID'], ['Username' => ['LIKE', "'".$_GET["user"]."'", '0']])[0][0];
            $vieweduserData = db_getUserData($vieweduser_id);

        } else {
            $vieweduser_id = $_SESSION['connected'];
            $vieweduserData = $userData;
        }
    ?>
    <main>
        <?php include_once(ASIDE); ?>
        
        <div class="central">
            <div class="top-profil">

                <div class="pic-profil">
                    <img src="<?= $vieweduserData[9] ?>" id="pic">
                </div>

                <div class="number-profil">
                    <div class="username-profil">
                        <div><?= $vieweduserData[2] ?> <?= $vieweduserData[1] ?></div>
                        <?php if($vieweduser_id == $_SESSION['connected']) { ?>
                        <a href="<?= SETTINGS ?>">
                            <ion-icon name="settings"></ion-icon>
                        </a>
                        <?php }else if(in_array(array($vieweduser_id), db_getFriends($_SESSION['connected']))){ ?>

                            <button id="send-messages" class="button_profil" onclick="submitFormConvLinkProfil()">
                                Send Messages
                            </button>
                            <form id="form-conversation-link-profil" method="GET" action="<?= CONVERSATION ?>">
                                 <input type="hidden" name="user_conv" value="<?= $vieweduserData[0] ?>">
                            </form>

                        <?php } else if(!in_array(array($vieweduser_id), db_getFriendRequest($_SESSION['connected']))){ ?>

                            <button id="send-f-request" class="button_profil" onclick="submitFormSendFriendReq()">
                                Send Friend Request
                            </button>
                            <form id="form-send-f-req" method="POST" action="<?= PHP.FRIEND_PRO ?>">
                                 <input type="hidden" name="SendReqUser" value="<?= $vieweduserData[0] ?>">
                            </form>

                        <?php } ?>
                    </div>
                    <div class="statistique-profil">
                        <?php 
                            $postLikes = db_selectColumns('post',['NumberOfLikes'],['PostedBy_UserID' => ['=', "'".$vieweduser_id."'", '0']]);
                            $nbPosts = count($postLikes);
                            $nbFriends = count(db_selectColumns('friends',['UserID_1'],
                                ['UserID_1' => ['=', "'".$vieweduser_id."'", '2'],
                                'UserID_2' => ['=', "'".$vieweduser_id."'", '0'] ]));
                            $nbLikes = 0;
                            for ($i=0; $i < $nbPosts; $i++) { 
                                $nbLikes += $postLikes[$i][0];
                            }
                        ?>
                        <div class="posts case-number">
                            Posts
                            <p><?= $nbPosts ?></p>
                        </div>
                        <div class="following case-number">
                            Friends
                            <p><?= $nbFriends ?></p>
                        </div>
                        <div class="followers case-number">
                            Likes
                            <p><?= $nbLikes ?></p>
                        </div>
                    </div>
                </div>
            </div>

             <hr><br>
            <div class="bottom-profil">
                
                <div class="container-btn-post">
                    <btn id="box1" >My Posts</btn>
                    <btn id="box2" >Liked Posts</btn>
                    <btn id="box3" >Shared Posts</btn>
                </div>

                <div class="content-posts">
                    <div id="content1" >
                            <div class="post-profil">
                            <?php 
                            $posts = db_selectColumns('post', ['*'], ['PostedBy_UserID' => ['LIKE', "'".$vieweduser_id."'", '0']]);
                            if(count($posts) == 0) {
                                ?><p>Vous n'avez encore fait aucun post</p><?php
                            }
                            for ($i=0; $i < count($posts) ; $i++) {
                                $postData = $posts[$i]; 
                                $postUserData = db_getUserData($postData[7]);
                                $postData[6] = urldecode($postData[6]);
                                $postData[5] = urldecode($postData[5]);
                            ?>
                            <div class="post-container">
                                <div class="user-profil">
                                    <img src="<?= $postUserData[9] ?>" id="profil-pic">
                                    <div>
                                        <p><?= $postUserData[0] ?></p>
                                        <span><?= $postData[1] ?></span>
                                    </div>
                                </div>
                                <p class="post-text"><?= $postData[6] ?></p>
                                <div class="post-media">
                                    <img src="images/feed-image-1.png" class="post-img">

                                    <div class="post-reactions">
                                        <div>
                                            <ion-icon name="heart" onclick="AddHeart()"></ion-icon>
                                            <small><?= $postData[2] ?></small>
                                        </div>
                                        <?php 
                                            $postComments = db_selectColumns('comment', ['*'], ['ReplyTo_PostID' => ['=', "'".$postData[0]."'", '0']]);
                                            $nbComments = count($postComments);                            
                                        ?>
                                        <div>
                                            <ion-icon id="menu<?=$i+1?>" name="chatbox-ellipses"
                                                onclick="CommentSectionOpen(this.id)">
                                            </ion-icon>
                                            <small><?= $nbComments ?></small>
                                        </div>
                                        <!-- Comments -->
                                        <?php 
                                            for ($j=0; $j < $nbComments ; $j++) {
                                                $postCommentData = $postComments[$j]; 
                                                $postCommentData[2] = urldecode($postCommentData[2]);
                                                $CommentUserData = db_getUserData($postCommentData[3]);
                                        ?>
                                        <div class="comment-menu" id="close<?=$i+1?>">
                                            <div class="comments-list">
                                                <div class="user-profil comment-box">
                                                    <img src="<?= $CommentUserData[9] ?>">
                                                    <div class="">
                                                        <div class="comment-pseudo-text">
                                                            <a class="comment-pseudo"><?= $CommentUserData[0] ?></a>
                                                            <p class="comment-text"><?= $postCommentData[2] ?></p>
                                                        </div>
                                                        <div class="comment-reaction">
                                                            <p class="comment-react comment-info">Répondre</p>
                                                            <!-- <p class="comment-react comment-info">Aimer</p> -->
                                                            <p class="comment-info"><?= $postCommentData[1] ?></p>
                                                        </div>
                                                    </div>
                                                    <!-- <div class="comment-number-like">
                                                        <p>
                                                            <ion-icon name="heart"></ion-icon><small>26</small>
                                                        </p>
                                                    </div> -->
                                                </div>
                                                <!-- <div class="load-comments">
                                                    <div class="comments-bar"></div>
                                                    <span class="show-more-comments">Afficher les 4 réponses</span>
                                                </div> -->
                                            </div>
                                        </div>
                                        <?php } ?>
                                        <div>
                                            <ion-icon name="share-social"></ion-icon>
                                            <small><?= $postData[3] ?></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                    
                    <div id="content2" style="display:none;">Content for LIKED POSTS
                    </div>

                    <div id="content3" style="display:none;">Content for SHARED POSTS
                    </div>
                </div>
            </div> 
        </div>  
        <!-- Right Content -->
        <?php 
            if($vieweduser_id == $_SESSION['connected'])
                define('CONVERSIONABLE','1');          
            $friends_id = db_getFriends($vieweduser_id);
            define('ARRAYFRIEND','1');
            $onclickfct = 'submitFormProfilLink';
            include_once(LISTFRIEND);
        ?>    
        


    </main>
   
</body>
<?php include_once(FOOTER); ?>
<script type="text/javascript" src="./js/profil.js"></script>

</html>
