@extends('logistics.app')

@section('title','Messages — LIKHAE Logistics')


@section('content')


@php

$conversations = [

'juan'=>[

'name'=>'Juan Dela Cruz',
'type'=>'Rider',
'status'=>'Online',
'avatar'=>'rider',

'messages'=>[

[
'type'=>'received',
'text'=>'Good morning. The parcel has been picked up from the seller.',
'time'=>'10:32 AM'
],

[
'type'=>'sent',
'text'=>'Confirmed. Please proceed to the sorting center.',
'time'=>'10:34 AM'
],

[
'type'=>'received',
'text'=>'Parcel has arrived at the sorting center.',
'time'=>'10:35 AM'
]

]

],




'seller'=>[

'name'=>'ABC Handmade Store',
'type'=>'Seller',
'status'=>'Online',
'avatar'=>'seller',

'messages'=>[

[
'type'=>'received',
'text'=>'The parcel is ready for pickup.',
'time'=>'9:20 AM'
],

[
'type'=>'sent',
'text'=>'A rider has been assigned.',
'time'=>'9:25 AM'
],

[
'type'=>'received',
'text'=>'Thank you. Parcel is prepared.',
'time'=>'9:27 AM'
]

]

],





'buyer'=>[

'name'=>'Maria Santos',
'type'=>'Buyer',
'status'=>'Offline',
'avatar'=>'buyer',

'messages'=>[

[
'type'=>'received',
'text'=>'Where is my order?',
'time'=>'Yesterday'
],

[
'type'=>'sent',
'text'=>'Your parcel is currently being processed.',
'time'=>'Yesterday'
]

]

]


];

@endphp








<div class="flex flex-col gap-8">



<section>

<span class="text-xs font-bold uppercase tracking-widest text-primary">
Communication Center
</span>


<h1 class="mt-3 text-4xl font-bold text-ink">
Messages
</h1>


<p class="mt-3 text-base text-muted">
Manage conversations between logistics, riders, sellers, and buyers.
</p>


</section>








<section
class="
grid
min-h-[750px]
overflow-hidden
rounded-3xl
border
border-line
bg-surface

xl:grid-cols-[380px_1fr]

"
>







{{-- LEFT CHAT LIST --}}

<aside
class="
border-r
border-line
"
>


<div class="p-6 border-b border-line">


<h2 class="text-xl font-bold text-ink">
Chats
</h2>


<input
placeholder="Search Messages..."
class="
mt-5
w-full
rounded-full
border
border-line
bg-page
px-5
py-3
text-sm
"
/>


</div>








<div
class="
divide-y
divide-line
"
>



@foreach($conversations as $key=>$chat)


<div

onclick="openChat('{{ $key }}')"

class="
chat-user
cursor-pointer
p-6
transition
hover:bg-page-secondary
"

id="user-{{ $key }}"

>



<div class="flex gap-4">


<div class="grid h-14 w-14 place-items-center rounded-full bg-primary-soft text-primary">
@if($chat['avatar']==='rider')
<svg viewBox="0 0 24 24" class="h-5 w-5 fill-none stroke-current stroke-[1.6]"><path d="M5 17H3a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11a2 2 0 0 1 2 2v3"></path><rect x="9" y="11" width="14" height="10" rx="2"></rect><circle cx="12" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle></svg>
@elseif($chat['avatar']==='seller')
<svg viewBox="0 0 24 24" class="h-5 w-5 fill-none stroke-current stroke-[1.6]"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
@elseif($chat['avatar']==='buyer')
<svg viewBox="0 0 24 24" class="h-5 w-5 fill-none stroke-current stroke-[1.6]"><circle cx="12" cy="8" r="4"></circle><path d="M4 21c0-5 3-8 8-8s8 3 8 8"></path></svg>
@endif
</div>




<div class="flex-1">


<div class="flex justify-between">


<h3
class="
font-bold
text-ink
"
>

{{ $chat['name'] }}

</h3>


<span
class="
text-xs
text-muted
"
>
{{ $chat['messages'][count($chat['messages'])-1]['time'] }}
</span>


</div>



<p
class="
text-sm
font-semibold
text-primary
"
>
{{ $chat['type'] }}
</p>




<p
class="
mt-2
line-clamp-1
text-sm
text-muted
"
>

{{ $chat['messages'][count($chat['messages'])-1]['text'] }}

</p>



</div>



</div>


</div>


@endforeach



</div>




</aside>









{{-- RIGHT SIDE --}}

<section
class="
flex
flex-col
"
>





{{-- EMPTY STATE --}}

<div
id="emptyChat"

class="
flex
flex-1
items-center
justify-center
"
>


<div class="text-center">


<div class="mx-auto grid h-20 w-20 place-items-center rounded-full bg-primary-soft text-primary">
<svg viewBox="0 0 24 24" class="h-8 w-8 fill-none stroke-current stroke-[1.6]"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
</div>


<h2
class="
mt-5
text-xl
font-bold
text-ink
"
>
Select a conversation
</h2>


<p class="mt-2 text-muted">
Choose someone to start chatting.
</p>


</div>


</div>









{{-- CHAT WINDOW --}}

<div
id="chatWindow"

class="
hidden
flex
flex-1
flex-col
"
>






<header
class="
flex
items-center
justify-between
border-b
border-line
p-6
"
>


<div class="flex gap-4 items-center">


<div
id="chatAvatar"
class="grid h-14 w-14 place-items-center rounded-full bg-primary-soft text-primary"
>
</div>




<div>


<h2
id="chatName"

class="
text-xl
font-bold
text-ink
"
>
</h2>



<p
id="chatStatus"

class="
text-sm
text-muted
"
>
</p>



</div>


</div>




<span
class="
rounded-full
bg-success-soft
px-4
py-2
text-xs
text-success
"
>
Active
</span>



</header>









<div
id="messageBox"

class="
flex-1
space-y-5
overflow-y-auto
bg-page
p-8
"
>



</div>









<footer
class="
border-t
border-line
p-6
"
>


<div class="flex gap-4">


<input

id="messageInput"

placeholder="Type a message..."

class="
flex-1
rounded-full
border
border-line
bg-page
px-6
py-4
"
/>



<button

onclick="sendMessage()"

class="
rounded-full
bg-primary
px-8
text-white
font-semibold
"
>

Send

</button>


</div>



</footer>





</div>






</section>




</section>



</div>









<script>


const conversations = @json($conversations);

const avatarIcons = {
    rider: `<svg viewBox="0 0 24 24" class="h-6 w-6 fill-none stroke-current stroke-[1.6]"><path d="M5 17H3a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11a2 2 0 0 1 2 2v3"></path><rect x="9" y="11" width="14" height="10" rx="2"></rect><circle cx="12" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle></svg>`,
    seller: `<svg viewBox="0 0 24 24" class="h-6 w-6 fill-none stroke-current stroke-[1.6]"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>`,
    buyer: `<svg viewBox="0 0 24 24" class="h-6 w-6 fill-none stroke-current stroke-[1.6]"><circle cx="12" cy="8" r="4"></circle><path d="M4 21c0-5 3-8 8-8s8 3 8 8"></path></svg>`,
};





let activeChat=null;



function openChat(id)
{


activeChat=id;


let chat=conversations[id];



document
.getElementById('emptyChat')
.classList
.add('hidden');



document
.getElementById('chatWindow')
.classList
.remove('hidden');




document
.getElementById('chatName')
.innerHTML=chat.name;



document
.getElementById('chatStatus')
.innerHTML=
chat.type+" • "+chat.status;



document.getElementById('chatAvatar').innerHTML = avatarIcons[chat.avatar] ?? '';



let box=
document.getElementById('messageBox');



box.innerHTML="";





chat.messages.forEach(message=>{


let side =
message.type==="sent"
?
"justify-end"
:
"justify-start";



let bubble =
message.type==="sent"
?
"bg-primary text-white"
:
"bg-surface text-ink";



box.innerHTML += `

<div class="flex ${side}">


<div class="
max-w-md
rounded-2xl
p-5
${bubble}
">


<p class="text-sm">
${message.text}
</p>


<span class="block mt-2 text-xs opacity-70">
${message.time}
</span>


</div>


</div>

`;



});



box.scrollTop=box.scrollHeight;



}







function sendMessage()
{


let input =
document.getElementById('messageInput');



if(input.value.trim()==="")
return;




conversations[activeChat].messages.push({

type:'sent',

text:input.value,

time:'Now'

});



input.value="";


openChat(activeChat);



}





</script>




@endsection