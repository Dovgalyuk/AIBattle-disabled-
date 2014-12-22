#include "LogChooser.h"

LogChooser::LogChooser(QFrame *parent)
	: QGroupBox(parent)
{
	selectButton = new QPushButton;
    lineEdit = new QLineEdit;
    QVBoxLayout* vlt = new QVBoxLayout;
    QHBoxLayout* hlt = new QHBoxLayout;

	lineEdit->setReadOnly(true) ;
    selectButton->setIcon(style()->standardIcon(QStyle::SP_DialogOpenButton));

	hlt->addWidget(lineEdit);
    hlt->addWidget(selectButton);
    vlt->addLayout(hlt);

	setLayout(vlt);
	setTitle("Log:");

	// Слоты
    QObject::connect(selectButton, SIGNAL(clicked()), this, SLOT(openFile())) ;
}

LogChooser::~LogChooser()
{

}

QString LogChooser::getLogPath()
{
	return lineEdit->text();
}

void LogChooser::clearLogPath()
{
	lineEdit->clear();
}

void LogChooser::openFile()
{
    QString fileName = QFileDialog::getOpenFileName(this, tr("Open File"),
                                                    "",
                                                    tr("Text (*.txt);;Log (*.log);;All (*.*)"));
    lineEdit->setText(fileName);
	emit readyToDrawField();
}

void LogChooser::setLogPath(const QString path)
{
    lineEdit->setText(path);
	emit readyToDrawField();
}

