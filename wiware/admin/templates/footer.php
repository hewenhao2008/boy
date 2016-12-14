<script>
    window.onload=function(){
        if(document.documentElement.scrollHeight <= document.documentElement.clientHeight) {
            bodyTag = document.getElementsByTagName('body')[0];
            bodyTag.style.height = document.documentElement.clientWidth / screen.width * screen.height + 'px';
        }
        setTimeout(function() {
            window.scrollTo(0, 1)
        }, 0);
    };
</script>

<div class="footer-wipark">
<strong>Copyright&nbsp;<i class="glyphicon glyphicon-copyright-mark"></i>&nbsp;2014 <?=$OEM?></strong>
</div>
</body>
</html>