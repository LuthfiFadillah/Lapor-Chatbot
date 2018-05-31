<html>
<head>
	<title>Botman Testing</title>
</head>
<body>

<p>test...</p>

<script>
    var is_production = false;
    var base_url = is_production ? window.location.origin : 'http://localhost:8080/Lapor-Chatbot-dev' ;
    var botmanWidget = {
        frameEndpoint: base_url+'/chat.html',
        introMessage: 'Hello, I am a Chatbot',
        chatServer : base_url+'/chat.php',
        title: 'My Chatbot', 
        mainColor: '#456765',
        bubbleBackground: '#ff76f4',
        aboutText: '',
        bubbleAvatarUrl: ''
    }; 
</script>
    <script src='https://cdn.jsdelivr.net/npm/botman-web-widget@0/build/js/widget.js'></script>

</body>
</html>