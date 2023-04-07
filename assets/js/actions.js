function rateVideo(video, type) {
 const url = "/api/rateVideo";
 const data = new FormData();
 data.append("video", video);
 data.append("type", type);
 
 fetch(url, {
  method: "POST",
  body: data,
 })
 .then(response => response.text())
 .then(responseData => {
  if(type == 0) {
   button = document.getElementById('like');
   btnicon = 'bi-hand-thumbs-up'
  } else {
   button = document.getElementById('dislike');
   btnicon = 'bi-hand-thumbs-down'
  }

  responseData = JSON.parse(responseData);

  if(responseData.response == 1) {
   button.classList.remove(btnicon);
   button.classList.add(btnicon+'-fill');
   
   button.innerText = ' '+responseData.rating;
  } else {
   button.classList.remove(btnicon+'-fill');
   button.classList.add(btnicon);
   
   button.innerText = ' '+responseData.rating;
  }

  if(responseData.response == 2) {
    button.innerText = " ಠ_ಠ";
   }
 });
    
}

function subscribe(user) {
    const url = "/api/subscribe";
    const data = new FormData();
    data.append("user", user);
    
    fetch(url, {
     method: "POST",
     body: data,
    })
    .then(response => response.text())
    .then(responseData => {
      subcount = document.getElementById('subcount');
      button = document.getElementById('btnsub');

      responseData = JSON.parse(responseData);

     if(responseData.response == 1) {
      subcount.innerText = responseData.value+' subscribers';
      button.innerText = 'Subscribed';
      button.classList.add('active');
     } else if(responseData.response == 0) {
      subcount.innerText = responseData.value+' subscribers';
      button.innerText = 'Subscribe';
      button.classList.remove('active');
     }
   
     if(responseData.response == 2) {
      button.innerText = " ಠ_ಠ";
      }
    });
       
}

function addComment(video) {
  var text = document.getElementById("comment-text").value;
  const url = "/api/addComment";
  const data = new FormData();
  data.append("video", video);
  data.append("text", text);
  
  fetch(url, {
   method: "POST",
   body: data,
  })
  .then(response => response.text())
  .then(responseData => {
    var comment = new DOMParser().parseFromString(responseData, 'text/html');
    document.getElementById("comments").prepend(comment.documentElement);
  });
 
};