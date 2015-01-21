#ifndef LOGCHOOSER_H
#define LOGCHOOSER_H

#include <QGroupBox>
#include <QString>
#include <QLabel>
#include <QPushButton>
#include <QVBoxLayout>
#include <QHBoxLayout>
#include <QLineEdit>
#include <QIcon>
#include <QObject>
#include <QStyle>
#include <QFileDialog>

class LogChooser : public QGroupBox
{
	Q_OBJECT

public:
	LogChooser(QFrame *parent = 0);
    QString getLogPath();
    void setLogPath(const QString path);
	void clearLogPath();
	~LogChooser();

private:
    QPushButton* selectButton ;
    QLineEdit* lineEdit ;
private slots:
    void openFile();
signals:
	void readyToDrawField();
};

#endif // LOGCHOOSER_H
