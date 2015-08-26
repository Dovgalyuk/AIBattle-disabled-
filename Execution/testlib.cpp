#include "testlib.h"

const char* LLONG_MIN_STR = "-9223372036854775808";

void InStream::skipBlanks()
{
    while (isBlanks(reader.curChar()))
        reader.skipChar();
}

char InStream::curChar()
{
    return char(reader.curChar());
}

char InStream::nextChar()
{
    return char(reader.nextChar());
}

char InStream::readChar()
{
    return nextChar();
}

bool InStream::eof()
{
    return reader.eof();
}

bool InStream::seekEof()
{
    skipBlanks();
    return eof();
}

bool InStream::eoln()
{
    int c = reader.nextChar();

    if (c == EOFC)
        return true;

    if (c == CR)
    {
        c = reader.nextChar();

        if (c != LF)
        {
            reader.unreadChar(c);
            reader.unreadChar(CR);
            return false;
        }
        else
            return true;
    }

    if (c == LF)
        return true;

    reader.unreadChar(c);
    return false;
}

bool InStream::seekEoln()
{
    int cur;
    do
    {
        cur = reader.nextChar();
    }
    while (cur == SPACE || cur == TAB);

    reader.unreadChar(cur);
    return eoln();
}

void InStream::readStringTo(std::string& result)
{
    skipBlanks();

    result.clear();
    int cur;

    for (;;)
    {
        cur = reader.curChar();

        if (isEoln(cur))
            break;

        if (isBlanks(cur))
            break;

        if (cur == EOFC)
            break;

        result += char(reader.nextChar());
    }

    eoln();

    if (result.size() == 0)
    {
        throw ReadCheckerException(RR_WF, "Expected non empty string");
    }
}

std::string InStream::readString()
{
    readStringTo(tmpReadToken);
    return tmpReadToken;
}

void InStream::readWordTo(std::string& result)
{
    skipBlanks();
    int cur = reader.nextChar();

    if (cur == EOFC)
    {
        throw ReadCheckerException(RR_WF,
                "Get end of file, expected word token");
    }

    if (isBlanks(cur))
    {
        throw ReadCheckerException(RR_WF,
                "Get blank spaces, expected word token");
    }

    result.clear();

    while (!isBlanks(cur) && cur != EOFC)
    {
        result += char(cur);
        cur = reader.nextChar();
    }

    reader.unreadChar(cur);

    if (result.length() == 0)
    {
        throw ReadCheckerException(RR_WF,
                "Get blank spaces or end of file, expected word token");
    }
}

bool InStream::equals(long long integer, const char* s)
{
    if (integer == LLONG_MIN)
        return strcmp(s, LLONG_MIN_STR) == 0;

    if (integer == 0LL)
        return strcmp(s, "0") == 0;

    size_t length = strlen(s);
    if (length == 0)
        return false;

    if (integer < 0 && s[0] != '-')
        return false;

    if (integer < 0)
    {
        s++;
        length--;
        integer = -integer;
    }

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

long long InStream::stringToLongLong(const char* buffer)
{
    if (strcmp(buffer, LLONG_MIN_STR) == 0)
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

int InStream::readInteger()
{
    if (seekEof())
    {
        throw ReadCheckerException(RR_WF, "Get end of file, expected data");
    }

    readWordTo(tmpReadToken);
    long long value = stringToLongLong(tmpReadToken.c_str());

    if (value < INT_MIN || value > INT_MAX)
    {
        throw ReadCheckerException(RR_WF,
                "Get " + tmpReadToken + "  - expected int32");
    }

    return int(value);
}

unsigned int InStream::readUnsignedInteger()
{
    if (seekEof())
    {
        throw ReadCheckerException(RR_WF, "Get end of file, expected data");
    }

    readWordTo(tmpReadToken);

    if (tmpReadToken.size() > 0 && tmpReadToken[0] == '-')
    {
        std::ostringstream out;
        out << "Get " << tmpReadToken << ", expected unsigned int";
        throw ReadCheckerException(RR_WF, out.str());
    }

    long long value = stringToLongLong(tmpReadToken.c_str());

    if (value < 0 || value > UINT_MAX)
    {
        throw ReadCheckerException(RR_WF,
                "Get " + tmpReadToken + "  - expected unsigned int");
    }

    return (unsigned int) ((value));
}

long long InStream::readLong()
{
    if (seekEof())
    {
        throw ReadCheckerException(RR_WF, "Get end of file, expected data");
    }

    readWordTo(tmpReadToken);
    return stringToLongLong(tmpReadToken.c_str());
}


InStream::InStream(const std::string &content)
{
    reader = StringInputStreamReader(content);
}

InStream::~InStream()
{
    reader.close();
}

template<> InStream& InStream::operator >> <int>(const ValueInBounds<int> &val)
{
    int streamValue = this->readInteger();

    if (val.isValueInBound(streamValue))
    {
        val.value = streamValue;
    }
    else
    {
        std::ostringstream out;
        out << "Expected value in range [" << val.min << ", " << val.max << "], get " << streamValue;
        throw ReadCheckerException(RR_WR, out.str());
    }

    return *this;

}

template<> InStream& InStream::operator>><long long>(const ValueInBounds<long long> &val)
{
    long long streamValue = this->readLong();

    if (val.isValueInBound(streamValue))
    {
        val.value = streamValue;
    }
    else
    {
        std::ostringstream out;
        out << "Expected value in range [" << val.min << ", " << val.max << "], get " << streamValue;
        throw ReadCheckerException(RR_WR, out.str());
    }

    return *this;
}

template<> InStream& InStream::operator>><unsigned int>(const ValueInBounds<unsigned int> &val)
{
    unsigned int streamValue = this->readUnsignedInteger();

    if (val.isValueInBound(streamValue))
    {
        val.value = streamValue;
    }
    else
    {
        std::ostringstream out;
        out << "Expected value in range [" << val.min << ", " << val.max << "], get " << streamValue;
        throw ReadCheckerException(RR_WR, out.str());
    }

    return *this;
}

template<> InStream& InStream::operator>> <char>(const ValueInRange<char> &val)
{
    std::string streamValue = this->readString();
    if (streamValue.size() == 0 || streamValue.size() > 1)
    {
        std::ostringstream out;
        out << "Get " << streamValue << ", expected char";
        throw ReadCheckerException(RR_WF, out.str());
    }
    else
    {
        if (val.isValueInRange(streamValue.at(0)))
        {
            val.value = streamValue.at(0);
        }
        else
        {
            std::ostringstream out;
            out << "Expected value in range " << val.getRangeTextPresentation() << ", get " << streamValue;
            throw ReadCheckerException(RR_WR, out.str());
        }
    }

    return *this;
}

template<> InStream& InStream::operator>> <int>(const ValueInRange<int> &val)
{
    int streamValue = this->readInteger();

    if (val.isValueInRange(streamValue))
    {
        val.value = streamValue;
    }
    else
    {
        std::ostringstream out;
        out << "Expected value in range " << val.getRangeTextPresentation() << ", get " << streamValue;
        throw ReadCheckerException(RR_WR, out.str());
    }

    return *this;
}

InStream& InStream::operator >>(int& value)
{
    value = this->readInteger();
    return *this;
}

InStream& InStream::operator >>(unsigned int& value)
{
    value = this->readUnsignedInteger();
    return *this;
}

InStream& InStream::operator >>(long long & value)
{
    value = this->readLong();
    return *this;
}

InStream& InStream::operator >>(std::string& value)
{
    value = this->readString();
    return *this;
}
