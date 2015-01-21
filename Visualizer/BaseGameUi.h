#ifndef BASEGAMEUI_H
#define BASEGAMEUI_H

#include <QWidget>
#include <QMainWindow>
#include <QLabel>
#include <QPushButton>
#include <QPainter>
#include <QLayout>
#include <QTextStream>
#include <QMessageBox>
#include <QEvent>
#include <QTimer>
#include <QScrollBar>
#include <QString>
#include "LogChooser.h"

struct LogItem
{
    enum LogItemKind
    {
        K_MOVE,
        K_ERROR,
        K_WIN,
        K_TIE,
        K_FIELD,
        K_ANIMATION
    };
    LogItemKind kind;
    int player;
    QString data;
    QString input;
};

class BaseGameUi : public QWidget
{
	Q_OBJECT
private:
    static const int MAX_TIMER_INTERVAL = 10000;
protected:
    static const int animationTimerInterval = 100;

private:
	QLabel *myText;
	QLabel *scoreText;
protected:
	QLabel *fieldLabel;
	QSize fieldBaseSize;
	QSize getFieldSize();  // Получение размеро Label'а
	QPixmap *getFieldPixmap(); // Получение pixmap

    
private:
	LogChooser *logChooser;
	QPushButton *nextTurnButton;
	QPushButton *prevTurnButton;
    QScrollBar *speedScroll;
    QPushButton *startButton;
    QPushButton* resetLogButton;
	QVector<LogItem> history; // история ходов
	int currentTurn; // Текущий ход
    QString gameName;
    QTimer *timer;
    QTimer *animationTimer;
    QString playerName1;
    QString playerName2;

    

    int animationTimerCount;
protected:
    void setScore(int score1, int score2);
    int getAnimationTimerCount();
private:
	virtual void prepareGame() {} // Подготовка игры
    virtual void move(int player, const QString &turn) = 0;
    virtual void setField(const QString &field) = 0;
    virtual void drawField() = 0; // Отрисовка поля
    virtual void setAnimation(const QString &animation) {}
private slots:
    // Следующий шаг из лога 
	void nextTurnHandler();
    // Предыдущий шаг из лога
	void prevTurnHandler();
    // Собираем ходы из лога
	void getTurnsFromLog();
	void switchAnimation();
    void speedChanged(int value); 
    void checkAnimation();
    void resetLog();
private:
    // сброс (инициализация) игры к первой позиции в логе
    void resetGame();
    // Следующий шаг из лога 
	void nextTurn();
    void startAnimation();
    void stopAnimation();
public:
	BaseGameUi(const QString& name, QSize fieldSize, QWidget *parent = 0, bool hasScore = false);
    ~BaseGameUi();
    void setPreLog(QString path);
};

#endif // BASEGAMEUI_H
