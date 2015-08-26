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

template <class T>
std::string numberToString(T value)
{
    std::ostringstream out;
    out << value;
    return out.str();
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
        case RR_OK:
            return "OK";
        default:
            return "Unknown";
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

    ValueInBounds(T &v, T minBound, T maxBound) : value(v)
    {
        min = minBound;
        max = maxBound;
    }

    bool isValueInBound(T streamValue) const
    {
        return streamValue >= min && streamValue <= max;
    }
};

template<class T>
struct ValueInRange
{
    T &value;
    T *range;
    unsigned int size;


    ValueInRange(T &v, T *r, unsigned int s) : value(v), size(s), range(NULL)
    {
        range = new T[size];
        std::copy(r, r + size, range);
    }

    ~ValueInRange()
    {
        delete[] range;
        range = NULL;
    }

    bool isValueInRange(T streamValue) const
    {
        bool isFail = true;

        for (unsigned int i = 0; i < size; ++i)
            if (range[i] == streamValue)
            {
                isFail = false;
                break;
            }

        return !isFail;
    }

    std::string getRangeTextPresentation() const
    {
        std::string result = "[ ";
        for (unsigned int i = 0; i < size; ++i)
        {
            result += numberToString(range[i]);
            if (i != size - 1)
                result += ", ";
        }

        result += " ]";
        return result;
    }
};

class StringInputStreamReader
{
private:
    std::string s;
    size_t pos;

public:
    StringInputStreamReader(const std::string& content) :  s(content), pos(0)
    {
        // No operations.
    }

    StringInputStreamReader() : s(""), pos(0)
    {

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
    StringInputStreamReader reader;
    std::string tmpReadToken;

    /* Moves stream pointer to the first non-white-space character or EOF. */
    void skipBlanks();

    /* Returns current character in the stream. Doesn't remove it from stream. */
    char curChar();

    /* Returns current character and moves pointer one character forward. */
    char nextChar();

    /* Returns current character and moves pointer one character forward. */
    char readChar();

    /* Checks that current position is EOF. If not it doesn't move stream pointer. */
    bool eof();

    /* Moves pointer to the first non-white-space character and calls "eof()". */
    bool seekEof();

    /*
     * Checks that current position contains EOLN.
     * If not it doesn't move stream pointer.
     */
    bool eoln();

    /* Moves pointer to the first non-space and non-tab character and calls "eoln()". */
    bool seekEoln();

    /*
     * Reads line from the current position to EOLN or EOF. Moves stream pointer to
     * the first character of the new line (if possible).
     */
    void readStringTo(std::string& result);
    std::string readString();

    /*
     * Reads new token.
     */
    void readWordTo(std::string& result);

    bool equals(long long integer, const char* s);

    /*
     * Transfer string to long long
     */
    long long stringToLongLong(const char* buffer);

    /*
     * Reads new int. Ignores white-spaces
     */
    int readInteger();

    /*
     * Reads new unsigned int. Ignores white-spaces
     */
    unsigned int readUnsignedInteger();

    /*
     * Reads new long long value. Ignores white-spaces
     */
    long long readLong();

public:
    InStream(const std::string &content);
    ~InStream();

    InStream& operator >>(int& value);

    InStream& operator >>(unsigned int& value);

    InStream& operator >>(long long & value);

    InStream& operator >>(std::string& value);

    template<class T> InStream& operator>>(const ValueInBounds<T> &val);
    template<class T> InStream& operator>>(const ValueInRange<T> &val);

};

#endif
