awk '
function char2hex(char){
    for(j=0;j<256;j++)
    {
        tmp=sprintf("%c",j);
        if(tmp==char){
            return sprintf("%%%X",j);
        }
    }
    return char;
}
BEGIN{
    FS="";
    OFS="";
}
{
    for(i=1;i<=NF;i++) $i=char2hex($i);
    print;
}
'
