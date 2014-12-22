#include "BaseGameUi.h"

BaseGameUi::BaseGameUi(const QString& name, QSize fieldSize, QWidget *parent, bool hasScore)
	: QWidget(parent), fieldBaseSize(fieldSize), scoreText(NULL), gameName(name), playerName1(""), playerName2(""), animationTimerCount(0)
{

	myText = new QLabel(gameName);
	myText->setAlignment(Qt::AlignCenter);
	myText->setFont(QFont("Times", 12, 100));
    if (hasScore)
    {
	    scoreText = new QLabel("0 : 0");
	    scoreText->setAlignment(Qt::AlignCenter);
	    scoreText->setFont(QFont("Times", 12, 100));
    }

	nextTurnButton = new QPushButton("Next");
	nextTurnButton->setEnabled(false);
	prevTurnButton = new QPushButton("Prev");
	prevTurnButton->setEnabled(false);
	fieldLabel = new QLabel;
	logChooser = new LogChooser;

	fieldLabel->setScaledContents(true);
	fieldLabel->setFrameShape(QFrame::Box);

    speedScroll = new QScrollBar(Qt::Horizontal);
    speedScroll->setRange(1, 100);
    speedScroll->setSliderPosition(10);
    startButton = new QPushButton("Start");
	startButton->setEnabled(false);
    resetLogButton = new QPushButton("Reset");
    resetLogButton->setEnabled(false);
	QHBoxLayout* hrzTimer = new QHBoxLayout;
	hrzTimer->addWidget(new QLabel("Speed:"));
	hrzTimer->addWidget(speedScroll, 1);

    QVBoxLayout* btnLt = new QVBoxLayout;
    btnLt->addWidget(startButton);
    btnLt->addWidget(resetLogButton);

	hrzTimer->addLayout(btnLt);

    QHBoxLayout* hrzlt = new QHBoxLayout;
	hrzlt->addWidget(prevTurnButton);
	hrzlt->addWidget(nextTurnButton);

	QGridLayout* gridLayout = new QGridLayout;
    int row = 0;
	gridLayout->addWidget(myText, row++, 0);
    if (hasScore)
	    gridLayout->addWidget(scoreText, row++, 0);
	gridLayout->addWidget(fieldLabel, row++, 0);
	gridLayout->setRowStretch(row - 1, 1);
	gridLayout->addWidget(logChooser, row++, 0);
	gridLayout->addLayout(hrzlt, row++, 0);
	gridLayout->addLayout(hrzTimer, row++, 0);

	setLayout(gridLayout);

    timer = new QTimer(this);
	animationTimer = new QTimer(this);
    
    // Слоты
	connect(nextTurnButton, SIGNAL(clicked()), this, SLOT(nextTurnHandler()));
	connect(prevTurnButton, SIGNAL(clicked()), this, SLOT(prevTurnHandler()));
	connect(logChooser, SIGNAL(readyToDrawField()), this, SLOT(getTurnsFromLog()));

    connect(timer, SIGNAL(timeout()), this, SLOT(nextTurnHandler()));
    connect(animationTimer, SIGNAL(timeout()), this, SLOT(checkAnimation()));
    connect(startButton, SIGNAL(clicked()), this, SLOT(switchAnimation()));
    connect(resetLogButton, SIGNAL(clicked()), this, SLOT(resetLog()));
    connect(speedScroll, SIGNAL(valueChanged(int)), this, SLOT(speedChanged(int)));
}

BaseGameUi::~BaseGameUi()
{
}

QPixmap* BaseGameUi::getFieldPixmap()
{
	return const_cast<QPixmap*>(fieldLabel->pixmap());
}

QSize BaseGameUi::getFieldSize()
{
	return fieldLabel->pixmap()->size();
}

void BaseGameUi::setScore(int score1, int score2)
{
    if (scoreText)
        scoreText->setText(QString("%1 : %2").arg(score1).arg(score2));
}

void BaseGameUi::resetGame()
{
	currentTurn = 0;
	prepareGame();
    if (!history.empty() && history.front().kind == LogItem::K_FIELD)
    {
        setField(history.front().data);
        ++currentTurn;
    }
}

void BaseGameUi::nextTurnHandler()
{
    nextTurn();
    if (currentTurn < history.size())
    {
        animationTimerCount = 0;
        animationTimer->setInterval(MAX_TIMER_INTERVAL / speedScroll->value() / animationTimerInterval);
        animationTimer->start();
    }
    drawField();
}


void BaseGameUi::nextTurn()
{
	if (currentTurn < history.size())
    {
        if (history[currentTurn].kind == LogItem::K_MOVE)
	    {
            move(history[currentTurn].player, history[currentTurn].data);
            // check field
            ++currentTurn;
            if (history[currentTurn].kind == LogItem::K_ANIMATION)
            {
                setAnimation(history[currentTurn].data);
                ++currentTurn;
            }
    	    if (history[currentTurn].kind == LogItem::K_FIELD)
            {
                setField(history[currentTurn].data);
                ++currentTurn;
            }
            // check win/tie
    	    if (history[currentTurn].kind == LogItem::K_WIN)
            {
                stopAnimation();
    	        drawField();
                int winner = history[currentTurn].player;
                QString winnerString;

                if (playerName1 == "" && playerName2 == "")
                    winnerString = QString::number(winner);
                else
                {
                    if (winner == 1) 
                        winnerString = playerName1;
                    else
                        winnerString = playerName2;
                }

			    QMessageBox::information(this, "Winner!", winnerString + QString(" is a winner!"));
	            ++currentTurn;
            }
    	    else if (history[currentTurn].kind == LogItem::K_TIE)
            {
                stopAnimation();
    	        drawField();
			    QMessageBox::information(this, "Tie!", QString("No winner!"));
	            ++currentTurn;
            }
	    }
	    // Ошибки
	    else if (history[currentTurn].kind == LogItem::K_ERROR)
    	{
            stopAnimation();
		    QMessageBox::critical(this, "TL", QString("Time limit or some other error!"));
	        ++currentTurn;
	    }
    }
    if (currentTurn >= history.size()) 
    {
        stopAnimation();
        nextTurnButton->setEnabled(false);
        resetLogButton->setEnabled(true);
        startButton->setEnabled(false);
    }

	if (!prevTurnButton->isEnabled()) 
        prevTurnButton->setEnabled(true);
}

void BaseGameUi::prevTurnHandler()
{
    int newCurrentTurn = currentTurn - 1;
    while (newCurrentTurn > 0 && history[newCurrentTurn].kind != LogItem::K_MOVE)
        --newCurrentTurn;

    resetGame();

	while (currentTurn < newCurrentTurn)
	{
        nextTurn();
	}
	drawField();

	if (newCurrentTurn == 0
        || (newCurrentTurn == 1 && history[0].kind == LogItem::K_FIELD))
        prevTurnButton->setEnabled(false);

    if (!nextTurnButton->isEnabled()) 
    {
        nextTurnButton->setEnabled(true);
        startButton->setEnabled(true);
    }
}

void BaseGameUi::getTurnsFromLog()
{
	QFile logFile;
	QTextStream logStream;

    stopAnimation();
    playerName1 = "";
    playerName2 = "";

	if (!logFile.isOpen())
	{
		logFile.setFileName(logChooser->getLogPath());
		if (!logFile.open(QIODevice::ReadOnly | QIODevice::Text)) 
			return;
		logStream.setDevice(&logFile);
	}

	history.clear();
    myText->setText(gameName);

    QString lastInput;
	while (!logStream.atEnd())
	{
		QString line = logStream.readLine();
        QTextStream lineStream(&line);
        QString kind;
        lineStream >> kind;
        LogItem item;
        if (kind == "PLAYERS")
        {
            playerName1 = logStream.readLine();
            playerName2 = logStream.readLine();
            myText->setText(QString("%1 : %2 vs %3").arg(gameName).arg(playerName1).arg(playerName2));
            continue;
        }
        if (kind == "OK")
        {
            item.kind = LogItem::K_MOVE;
            lineStream >> item.player;
            while (!logStream.atEnd())
            {
                QString temp = logStream.readLine();
                if (temp == "END_OF_OUTPUT")
                    break;
                item.data += temp + "\n";
            }
            item.input = lastInput;
            lastInput = "";
        }
        else if (kind == "FIELD")
        {
            item.kind = LogItem::K_FIELD;
            while (!logStream.atEnd())
            {
                QString temp = logStream.readLine();
                if (temp == "END_OF_FIELD")
                    break;
                item.data += temp + "\n";
            }
        }
        else if (kind == "INPUT")
        {
            while (!logStream.atEnd())
            {
                QString temp = logStream.readLine();
                if (temp == "END_OF_INPUT")
                    break;
                lastInput += temp + "\n";
            }
            continue;
        }
        else if (kind == "WIN")
        {
            item.kind = LogItem::K_WIN;
            lineStream >> item.player;
        }
        else if (kind == "TIE")
        {
            item.kind = LogItem::K_TIE;            
        }
        else if (kind == "ANIMATION")
        {
            item.kind = LogItem::K_ANIMATION;
            while (!logStream.atEnd())
            {
                QString temp = logStream.readLine();
                if (temp == "END_OF_ANIMATION")
                    break;
                item.data += temp + "\n";
            }
        }
        else
        {
            item.kind = LogItem::K_ERROR;
            if (kind == "TL" || kind == "RE" || kind == "ML" || kind == "SV")
            {
                lineStream >> item.player;
                item.data = line;
            }
            else if (kind == "IM")
            {
                lineStream >> item.player;
                while (!logStream.atEnd())
                {
                    QString temp = logStream.readLine();
                    if (temp == "END_OF_OUTPUT")
                        break;
                    item.data += temp + "\n";
                }
                item.input = lastInput;
                lastInput = "";
            }
        }
        history.push_back(item);
	}

    if (history.size())
    {
	    prevTurnButton->setEnabled(false);
	    nextTurnButton->setEnabled(true);
	    startButton->setEnabled(true);
    }
    else
    {
    	prevTurnButton->setEnabled(false);
	    nextTurnButton->setEnabled(false);
	    startButton->setEnabled(false);
    }
    resetLogButton->setEnabled(false);

    resetGame();
	drawField();
}

void BaseGameUi::startAnimation()
{
    startButton->setText("Stop");
    timer->start(MAX_TIMER_INTERVAL / speedScroll->sliderPosition());
}

void BaseGameUi::stopAnimation()
{
    startButton->setText("Start");
    timer->stop();
}

void BaseGameUi::switchAnimation()
{
    if (timer->isActive())
        stopAnimation();
    else
        startAnimation();
}

void BaseGameUi::speedChanged(int value)
{
    if (timer->isActive())
    {
        timer->setInterval(MAX_TIMER_INTERVAL / value);
        animationTimer->setInterval(timer->interval() / animationTimerInterval);
    }
}

void BaseGameUi::checkAnimation()
{
    if (animationTimerCount < animationTimerInterval)
        animationTimerCount++;
    else
        animationTimer->stop();

    drawField();
}

int BaseGameUi::getAnimationTimerCount()
{
    return animationTimerCount;
}

void BaseGameUi::resetLog()
{
    resetGame();
    resetLogButton->setEnabled(false);
    drawField();
    startButton->setEnabled(true);
}

void BaseGameUi::setPreLog(QString path)
{
    logChooser->setLogPath(path);
    nextTurnButton->setEnabled(true);
	startButton->setEnabled(true);
}