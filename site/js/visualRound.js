var duels = new Array();
var users = new Array();
var labels = new Array();
var downed = new Array();
var controls = new Array(["Начать"], ["Пауза"], ["Заново"]);

// Timers
var stepTime = 2000;
var changeTime = 1000;
var moveTime = 200;

var showedDuels = 0;
var ulen = 0;
var round = -1;
var lastUser1 = -1;
var lastUser2 = -1;
var u1, u2;
var randomSize = 1000;
var winCost = 2;
var tieCost = 1;
var winner, winnerRow, winnerCell, winnerStartLabel, winnerLabel, waiting;
var state = 0;
var oneshot = false;

// Colors
var green = "#99EE99";
var red = "#FFCBCB";
var yellow = "#EEEE99";
var baseColor = "#FFF";

// Sync
var busy = false;
var tie = false;

function rand(i)
{
    return parseInt(Math.random() * randomSize) % i;
}

// Find by id

function g(id)
{
    return document.getElementById(id);
}

function startVisualization()
{
    var usersList = $(".tableRow");
    ulen = usersList.length;

    for (i = 0; i < ulen; i++)
    {
        users[i] = parseInt( ( usersList[i].id ).substr(1) );
        labels[i] = users[i];
    }
}

function finalAction()
{
    state = 2;
    g("pause").innerHTML = controls[state];
    changeTitle("Раунд закончен");
}

function resetColors()
{
/*    for (i = winnerStartLabel; i >= winnerLabel; i--)
    {
        $("#r" + labels[i])[0].style.backgroundColor = baseColor;
    }*/
    $("#r" + winner)[0].style.backgroundColor = baseColor;
    $("#r" + waiting)[0].style.backgroundColor = baseColor;
    
    if (!tie) busy = false;
    
    if (state == 1 && !oneshot) 
        setTimeout("updateTable()", stepTime); 
    oneshot = false;
}

function startSecondFromTie()
{  
    resetColors();
    winner = waiting;
    winnerRow = $("#r" + winner);
    winnerCell = $("#c" + winner);
    winnerScore = parseInt( winnerCell.html() );
    winnerScore += tieCost;
    winnerLabel = parseInt(winnerRow[0].title);
    winnerStartLabel = winnerLabel;
    winnerCell.html( winnerScore );
    winnerRow[0].style.backgroundColor = green;
    tie = false;
    setTimeout("upUser()", moveTime);
}

function upUser()
{
    if ( (winnerLabel == 0 || parseInt( $("#c" + labels[winnerLabel - 1]).html() ) >= winnerScore) && !tie) 
    {
        setTimeout("resetColors()", changeTime);
        return 0;
    }
    
    if ( (winnerLabel == 0 || $("#c" + labels[winnerLabel - 1]).html() >= winnerScore + "") && tie)
    {
        setTimeout("startSecondFromTie()", changeTime);
        return 0;
    }

    var l = $("#r" + labels[winnerLabel]).html();
    var r = $("#r" + labels[winnerLabel - 1]).html();
    $("#r" + labels[winnerLabel]).html(r);
    $("#r" + labels[winnerLabel - 1]).html(l);
    
    g("r" + labels[winnerLabel]).id = "r" + labels[winnerLabel - 1] + "t";
    
    if (labels[winnerLabel - 1] != waiting) 
        g("r" + labels[winnerLabel - 1] + "t").style.backgroundColor = baseColor;
    else
        g("r" + labels[winnerLabel - 1] + "t").style.backgroundColor = red;
        // Not works for TIE :)
    
    g("r" + labels[winnerLabel - 1]).id = "r" + labels[winnerLabel];
    g("r" + labels[winnerLabel]).style.backgroundColor = green;
    g("r" + labels[winnerLabel - 1] + "t").id = "r" + labels[winnerLabel - 1];
    
    var t = labels[winnerLabel];
    labels[winnerLabel] = labels[winnerLabel - 1];
    winnerLabel --;
    labels[winnerLabel] = t;
    
    setTimeout("upUser()", moveTime);

}

// AJAX receivers

function changeTitle(data, status)
{
    var d = data.split('&');
    g("bottomBarTextOld").innerHTML = g("bottomBarText").innerHTML;
    g("bottomBarText").innerHTML = d[0] + ( (state != 2) ? (' VS ' + d[1]) : "" );
}

function changeRow(data, status)
{
    var d = data.split('&');
    if (parseInt(d[0]) == 0)
    {
        g("bottomBarText").innerHTML += ' (' + d[2] + ')';
        
        if (parseInt( d[1] ) != 0)
        {            
            winner = ( parseInt( d[1] ) == 1 ) ? (users[u1]) : (users[u2]);
            waiting = (winner == users[u1]) ? (users[u2]) : (users[u1]);
            g("r"+waiting).style.backgroundColor = red;
            winnerRow = $("#r" + winner);
            winnerCell = $("#c" + winner);
            winnerScore = parseInt( winnerCell.html() );
            winnerScore += winCost;
            winnerLabel = parseInt(winnerRow[0].title);
            winnerStartLabel = winnerLabel;
            winnerCell.html( winnerScore );
            winnerRow[0].style.backgroundColor = green;
            setTimeout("upUser()", moveTime);
        }
        else
        {
            // For TIE
            winner = ( parseInt( $("#r" + users[u1])[0].title ) < parseInt( $("#r" + users[u2])[0].title ) ) ? (users[u1]) : (users[u2]);
            waiting = ( winner == users[u1] ) ? (users[u2]) : (users[u1]);
            
            winnerRow = $("#r" + winner);
            winnerCell = $("#c" + winner);
            winnerScore = parseInt( winnerCell.html() );
            winnerScore += tieCost;
            winnerLabel = parseInt(winnerRow[0].title);
            winnerStartLabel = winnerLabel;
            winnerCell.html( winnerScore );
            winnerRow[0].style.backgroundColor = green;
            tie = true;
            setTimeout("upUser()", moveTime);
        
        }
        
        showedDuels ++;
        //if (state == 1 && !oneshot) 
        //    setTimeout("updateTable()", stepTime); 
    }
    else
        alert("API Exception: post this message to Administrator\n"+data);
}

function updateTable()
{

    // Synch patch
    if (busy) 
    {
        if (state == 1) setTimeout("updateTable()", stepTime / 2);
        return 0;
    }
    
    busy = true;

    if (showedDuels == ulen * (ulen - 1)) 
    {
        finalAction();
        return 0;
    }
    
    u1 = rand(ulen);
    u2 = rand(ulen);
    
    while (duels[u1 + ":" + u2] || u1 == u2)
    {
        u1 = rand(ulen);
        u2 = rand(ulen);    
    }
    
    $("#r" + users[u1])[0].style.backgroundColor = yellow;
    $("#r" + users[u2])[0].style.backgroundColor = yellow;

    duels[u1 + ":" + u2] = true;
    g("bottomCounter").innerHTML = '(' + (showedDuels + 1) + '/' + ulen * (ulen - 1) + ')';
    $.post("jqueryGetDuel.php", {"m" : 1, "user1" : users[u1], "user2" : users[u2]}, changeTitle)
    setTimeout('$.post("jqueryGetDuel.php", {"round" : round, "user1" : users[u1], "user2" : users[u2]}, changeRow)', stepTime * 2);

    //if (state == 1 && !oneshot)
    //    setTimeout("updateTable()", stepTime);
}

// Controls

function step()
{
    if (state == 2) return 0;
    oneshot = true;
    updateTable();
}

function pause()
{
    if (state == 2) location.reload();
    if (state == 0) 
    {
        state = 1;
        g("pause").innerHTML = controls[state];
        updateTable();
    
    }
    else
    {
        state = 0;
        g("pause").innerHTML = controls[state];
    }
}

function showTimersDialog()
{
    state = 1;
    pause();
    var tmp = parseInt(prompt("Время между дуэлями", stepTime));
    if (!(!tmp)) 
        stepTime = tmp;
}