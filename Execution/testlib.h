/*
 This header is modification of original testlib.h
 from: https://github.com/MikeMirzayanov/testlib
 */

#ifndef READCHECKER_H
#define READCHECKER_H

#include <cstdio>
#include <sstream>
#include <iostream>
#include <climits>
#include <cstdlib>
#include <string>
#include <cstring>
#include <exception>

#define LF ((char)10)
#define CR ((char)13)
#define TAB ((char)9)
#define SPACE ((char)' ')
#define EOFC (255)

#ifndef LLONG_MIN
#define LLONG_MIN   (-9223372036854775807LL - 1)
#endif

template<typename C>
inline bool isEof(C c)
{
    return c == EOFC;
}

template<typename C>
inline bool isEoln(C c)
{
    return (c == LF || c == CR);
}

template<typename C>
inline bool isBlanks(C c)
{
    return (c == LF || c == CR || c == SPACE || c == TAB);
}

enum ReadResult
{
    RR_OK, // All right
    RR_WR, // Wrong range
    RR_WF // Wrong format
};

class ReadCheckerException
{
private:
    ReadResult result;
    std::string message;
public:
    ReadCheckerException(ReadResult result, const std::string &message)
    {
        this->result = result;
        this->message = message;
    }

    ReadCheckerException(ReadResult result, const char* message)
    {
        this->result = result;
        this->message = std::string(message);

    }

    ~ReadCheckerException()
    {
    }

    ReadResult getReadResult()
    {
        return result;
    }

    const char* getReadResultText()
    {
        switch (result)
        {
        case RR_WF:
            return "Wrong format";
        case RR_WR:
            return "Wrong range";
        }
    }

    const char* what()
    {
        return message.c_str();
    }
};

template<class T>
struct ValueInBounds
{
    T &value;
    T min, max;

    ValueInBounds(T &v, T minBound, T maxBound) :
            value(v)
    {
        min = minBound;
        max = maxBound;
    }

    bool isValueInBound(T streamValue) const
    {
        return !(streamValue < min || streamValue > max);
    }
};

class InputStreamReader
{
public:
    virtual int curChar() = 0;
    virtual int nextChar() = 0;
    virtual void skipChar() = 0;
    virtual void unreadChar(int c) = 0;
    //virtual std::string getName() = 0;
    virtual bool eof() = 0;
    virtual void close() = 0;
    virtual ~InputStreamReader() = 0;
};

InputStreamReader::~InputStreamReader()
{
    // No operations.
}

class StringInputStreamReader: public InputStreamReader
{
private:
    std::string s;
    size_t pos;

public:
    StringInputStreamReader(const std::string& content) :
            s(content), pos(0)
    {
        // No operations.
    }

    int curChar()
    {
        if (pos >= s.length())
            return EOFC;
        else
        {
            return s[pos];
        }
    }

    int nextChar()
    {
        if (pos >= s.length())
            return EOFC;
        else
            return s[pos++];
    }

    void skipChar()
    {
        pos++;
    }

    void unreadChar(int c)
    {
        if (pos != 0)
        {
            pos--;
            if (pos < s.length())
                s[pos] = char(c);
        }
    }

    /*
     std::string getName()
     {
     return __testlib_part(s);
     }
     */

    bool eof()
    {
        return pos >= s.length();
    }

    void close()
    {

    }
};

class InStream
{
private:
    InputStreamReader* reader;
    std::string tmpReadToken;

    /* Moves stream pointer to the first non-white-space character or EOF. */
    void skipBlanks()
    {
        while (isBlanks(reader->curChar()))
            reader->skipChar();
    }

    /* Returns current character in the stream. Doesn't remove it from stream. */
    char curChar()
    {
        return char(reader->curChar());
    }

    /* Returns current character and moves pointer one character forward. */
    char nextChar()
    {
        return char(reader->nextChar());
    }

    /* Returns current character and moves pointer one character forward. */
    char readChar()
    {
        return nextChar();
    }

    /* Checks that current position is EOF. If not it doesn't move stream pointer. */
    bool eof()
    {
        if (reader == NULL)
            return true;

        return reader->eof();
    }

    /* Moves pointer to the first non-white-space character and calls "eof()". */
    bool seekEof()
    {
        if (reader == NULL)
            return true;
        skipBlanks();
        return eof();
    }

    /*
     * Checks that current position contains EOLN.
     * If not it doesn't move stream pointer.
     */
    bool eoln()
    {
        if (reader == NULL)
            return true;

        int c = reader->nextChar();

        if (c == EOFC)
            return true;

        if (c == CR)
        {
            c = reader->nextChar();

            if (c != LF)
            {
                reader->unreadChar(c);
                reader->unreadChar(CR);
                return false;
            }
            else
                return true;
        }

        if (c == LF)
            return true;

        reader->unreadChar(c);
        return false;
    }

    /* Moves pointer to the first non-space and non-tab character and calls "eoln()". */
    bool seekEoln()
    {
        if (reader == NULL)
            return true;

        int cur;
        do
        {
            cur = reader->nextChar();
        }
        while (cur == SPACE || cur == TAB);

        reader->unreadChar(cur);
        return eoln();
    }

    /*
     * Reads line from the current position to EOLN or EOF. Moves stream pointer to
     * the first character of the new line (if possible).
     */
    void readStringTo(std::string& result)
    {
        if (reader == NULL)
            throw ReadCheckerException(RR_WF, "String expected");

        skipBlanks();

        result.clear();
        int cur;

        for (;;)
        {
            cur = reader->curChar();

            if (isEoln(cur))
                break;

            if (isBlanks(cur))
                break;

            if (cur == EOFC)
                break;

            result += char(reader->nextChar());
        }

        eoln();

        if (result.size() == 0)
            throw ReadCheckerException(RR_WF, "Expected non empty string");
    }

    std::string readString()
    {
        readStringTo(tmpReadToken);
        return tmpReadToken;
    }

    /*
     * Reads new token.
     */
    void readWordTo(std::string& result)
    {
        skipBlanks();

        int cur = reader->nextChar();

        if (cur == EOFC)
            throw ReadCheckerException(RR_WF,
                    "Get end of file, expected word token");

        if (isBlanks(cur))
            throw ReadCheckerException(RR_WF,
                    "Get blank spaces, expected word token");

        result.clear();

        while (!(isBlanks(cur) || cur == EOFC))
        {
            result += char(cur);
            cur = reader->nextChar();
        }

        reader->unreadChar(cur);

        if (result.length() == 0)
            throw ReadCheckerException(RR_WF,
                    "Get blank spaces or end of file, expected word token");
    }

    bool equals(long long integer, const char* s)
    {
        if (integer == LLONG_MIN)
            return strcmp(s, "-9223372036854775808") == 0;

        if (integer == 0LL)
            return strcmp(s, "0") == 0;

        size_t length = strlen(s);

        if (length == 0)
            return false;

        if (integer < 0 && s[0] != '-')
            return false;

        if (integer < 0)
            s++, length--, integer = -integer;

        if (length == 0)
            return false;

        while (integer > 0)
        {
            int digit = int(integer % 10);

            if (s[length - 1] != '0' + digit)
                return false;

            length--;
            integer /= 10;
        }

        return length == 0;
    }

    /*
     * Transfer string to long long
     */
    long long stringToLongLong(const char* buffer)
    {
        if (strcmp(buffer, "-9223372036854775808") == 0)
            return LLONG_MIN;

        bool minus = false;
        size_t length = strlen(buffer);

        if (length > 1 && buffer[0] == '-')
            minus = true;

        if (length > 20)
        {
            std::ostringstream out;
            out << "Get " << buffer << " - integer expected";
            throw ReadCheckerException(RR_WF, out.str());
        }
        long long retval = 0LL;

        int zeroes = 0;
        int processingZeroes = true;

        for (int i = (minus ? 1 : 0); i < int(length); i++)
        {
            if (buffer[i] == '0' && processingZeroes)
                zeroes++;
            else
                processingZeroes = false;

            if (buffer[i] < '0' || buffer[i] > '9')
            {
                std::ostringstream out;
                out << "Get " << buffer << " - integer expected";
                throw ReadCheckerException(RR_WF, out.str());
            }

            retval = retval * 10 + (buffer[i] - '0');
        }

        if (retval < 0)
        {
            std::ostringstream out;
            out << "Get " << buffer << " - integer expected";
            throw ReadCheckerException(RR_WF, out.str());
        }

        if ((zeroes > 0 && (retval != 0 || minus)) || zeroes > 1)
        {
            std::ostringstream out;
            out << "Get " << buffer << " - integer expected";
            throw ReadCheckerException(RR_WF, out.str());
        }

        retval = (minus ? -retval : +retval);

        if (length < 19)
            return retval;

        if (equals(retval, buffer))
            return retval;
        else
        {
            std::ostringstream out;
            out << "Get " << buffer << " - int64 expected";
            throw ReadCheckerException(RR_WF, out.str());
        }
    }

    /*
     * Reads new int. Ignores white-spaces
     */
    int readInteger()
    {
        if (seekEof())
            throw ReadCheckerException(RR_WF, "Get end of file, expected data");

        readWordTo(tmpReadToken);

        long long value = stringToLongLong(tmpReadToken.c_str());
        if (value < INT_MIN || value > INT_MAX)
            throw ReadCheckerException(RR_WF,
                    "Get " + tmpReadToken + "  - expected int32");

        return int(value);
    }

    /*
     * Reads new int. Ignores white-spaces
     */
    int readUnsignedInteger()
    {
        if (seekEof())
            throw ReadCheckerException(RR_WF, "Get end of file, expected data");

        readWordTo(tmpReadToken);

        if (tmpReadToken.size() > 0 && tmpReadToken[0] == '-')
        {
            std::ostringstream out;
            out << "Get " << tmpReadToken << ", expected unsigned int";
            throw ReadCheckerException(RR_WF, out.str());
        }

        long long value = stringToLongLong(tmpReadToken.c_str());
        if (value < 0 || value > UINT_MAX)
            throw ReadCheckerException(RR_WF,
                    "Get " + tmpReadToken + "  - expected unsigned int");

        return (unsigned int) (value);
    }

    /*
     * Reads new long long value. Ignores white-spaces
     */
    long long readLong()
    {
        if (seekEof())
            throw ReadCheckerException(RR_WF, "Get end of file, expected data");

        readWordTo(tmpReadToken);

        return stringToLongLong(tmpReadToken.c_str());
    }

public:
    InStream(const std::string &content)
    {
        reader = new StringInputStreamReader(content);
    }

    ~InStream()
    {
        if (reader != NULL)
        {
            reader->close();
            delete reader;
            reader = NULL;
        }
    }

    InStream& operator>>(int &value)
    {
        value = this->readInteger();
        return *this;
    }

    InStream& operator>>(unsigned int &value)
    {
        value = this->readUnsignedInteger();
        return *this;
    }

    InStream& operator>>(long long &value)
    {
        value = this->readLong();
        return *this;
    }

    InStream& operator>>(std::string &value)
    {
        value = this->readString();
        return *this;
    }

    template<class T> InStream& operator>>(const ValueInBounds<T> &val)
    {
    }

};

template<> InStream& InStream::operator >><int>(const ValueInBounds<int> &val)
{
    int streamValue = this->readInteger();

    if (val.isValueInBound(streamValue))
        val.value = streamValue;
    else
    {
        std::ostringstream out;
        out << "Expected value in range [" << val.min << ", " << val.max << "], get " << streamValue;
        throw ReadCheckerException(RR_WR, out.str());
    }

    return *this;

}

template<> InStream& InStream::operator>><long long>(
        const ValueInBounds<long long> &val)
{
    long long streamValue = this->readLong();

    if (val.isValueInBound(streamValue))
        val.value = streamValue;
    else
    {
        std::ostringstream out;
        out << "Expected value in range [" << val.min << ", " << val.max << "], get " << streamValue;
        throw ReadCheckerException(RR_WR, out.str());
    }

    return *this;
}

template<> InStream& InStream::operator>><unsigned int>(
        const ValueInBounds<unsigned int> &val)
{
    unsigned int streamValue = this->readUnsignedInteger();

    if (val.isValueInBound(streamValue))
        val.value = streamValue;
    else
    {
        std::ostringstream out;
        out << "Expected value in range [" << val.min << ", " << val.max << "], get " << streamValue;
        throw ReadCheckerException(RR_WR, out.str());
    }

    return *this;
}

#endif
